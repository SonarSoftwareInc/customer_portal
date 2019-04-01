<?php
namespace App\Http\Controllers;
use App\Http\Requests\AddTopOffRequest;
use App\Traits\ListsPaymentMethods;
use Carbon\Carbon;
use Exception;
use View;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class DataUsageController extends Controller
{
    use ListsPaymentMethods;
    private $frameworkDataUsageController;
    public function __construct()
    {
        $this->frameworkDataUsageController = new \SonarSoftware\CustomerPortalFramework\Controllers\DataUsageController();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $historicalUsage = $this->getHistoricalUsage();
        $policyDetails = $this->getPolicyDetails();
        $currentUsage = $historicalUsage[0];
        $historicalUsage = json_encode($historicalUsage);
        $calculatedCap = $policyDetails->policy_cap_in_gigabytes + round($policyDetails->rollover_available_in_bytes/1000**3, 2) + round($policyDetails->purchased_top_off_total_in_bytes/1000**3, 2);
        if ($calculatedCap > 0) {
            $usagePercentage = round(($currentUsage['billable'] / $calculatedCap) * 100);
        } else {
            $usagePercentage = 0;
        }
        return view("pages.data_usage.index", compact('historicalUsage', 'policyDetails', 'currentUsage', 'calculatedCap', 'usagePercentage'));
    }

    /**
     * Show the top off purchase page
     * @return $this
     */
    public function showTopOff()
    {
        $policyDetails = $this->getPolicyDetails();
        if ($policyDetails->allow_user_to_purchase_capacity !== true) {
            return redirect()->back()->withErrors(utrans("errors.topOffNotAvailable"));
        }

        return view("pages.data_usage.add_top_off", compact('policyDetails'));
    }

    /**
     * Endpoint to add additional capacity if immediate payment is not required
     * @param AddTopOffRequest $request
     * @return $this
     */
    public function addTopOff(AddTopOffRequest $request)
    {
        $policyDetails = $this->getPolicyDetails();
        if ($policyDetails->allow_user_to_purchase_capacity !== true) {
            return redirect()->back()->withErrors(utrans("errors.topOffNotAvailable"));
        }

        try {
            $this->frameworkDataUsageController->purchaseTopOff(get_user()->account_id, $request->input('quantity'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(utrans("errors.failedToAddDataUsage"));
        }

        $this->clearTopOffCache();
        return redirect()->action("DataUsageController@index")->with('success', utrans("data_usage.successfullyAddedUsage"));
    }

    /**
     * Get cached usage based billing policy details. Cache is lower on this one in case service is changed.
     * @return mixed
     */
    private function getPolicyDetails()
    {
        if (!Cache::tags("usage_based_billing_policy_details")->has(get_user()->account_id)) {
            $policyDetails = $this->frameworkDataUsageController->getUsageBasedBillingPolicyDetails(get_user()->account_id, 3);
            Cache::tags("usage_based_billing_policy_details")->put(get_user()->account_id, $policyDetails, 10);
        }
        return Cache::tags("usage_based_billing_policy_details")->get(get_user()->account_id);
    }

    /**
     * Get cached historical data usage
     * @return mixed
     */
    private function getHistoricalUsage()
    {
        if (!Cache::tags("historical_data_usage")->has(get_user()->account_id)) {
            $dataUsage = $this->formatHistoricalUsageData(array_slice($this->frameworkDataUsageController->getAggregatedDataUsage(get_user()->account_id, 3), 0, 12));
            Cache::tags("historical_data_usage")->put(get_user()->account_id, $dataUsage, 60);
        }
        return Cache::tags("historical_data_usage")->get(get_user()->account_id);
    }

    /**
     * Format the data returned from the customer portal framework data usage controller into a usable format
     * @param $dataUsage
     * @return array
     */
    private function formatGranularDataUsage($dataUsage)
    {
        $graphData = [];
        $largestValue = 0;
        $dataToFormat = $dataUsage->granular->series;
        foreach ($dataToFormat as $datumToFormat) {
            foreach ($datumToFormat->in as $timestamp => $value) {
                if (!array_key_exists($timestamp, $graphData)) {
                    $graphData[$timestamp] = [
                        "in" => $value,
                        "out" => 0
                    ];
                } else {
                    $graphData[$timestamp]['in'] += $value;
                }

                if ($graphData[$timestamp]['in'] > $largestValue) {
                    $largestValue = $graphData[$timestamp]['in'];
                }
            }
            foreach ($datumToFormat->out as $timestamp => $value) {
                $graphData[$timestamp]['out'] += $value;
                if ($graphData[$timestamp]['out'] > $largestValue) {
                    $largestValue = $graphData[$timestamp]['out'];
                }
            }
        }

        //We need to convert all the values to some SI-suffixed value based on the largest value so that the graph is not shown in bytes.
        $suffixAndPower = $this->returnSiSuffixAndPower($largestValue);
        foreach ($graphData as $timestamp => $values) {
            $graphData[$timestamp] = [
                'in' => round($values['in'] / 1000**$suffixAndPower['power'], 2),
                'out' => round($values['out'] / 1000**$suffixAndPower['power'], 2),
            ];
        }

        return json_encode([
            'graphData' => $graphData,
            'suffix' => $suffixAndPower['suffix'],
        ]);
    }

    /**
     * Convert all historical usage to gigabytes
     * @param $historicalUsageData
     * @return array
     */
    private function formatHistoricalUsageData($historicalUsageData)
    {
        $formattedData = [];
        foreach ($historicalUsageData as $datum) {
            $timestamp = new Carbon($datum->start_time, "UTC");
            array_push($formattedData, [
                'timestamp' => $timestamp->toRfc3339String(),
                'billable' => round(($datum->billable_in_bytes+$datum->billable_out_bytes)/1000**3, 2),
                'free' => round(($datum->free_in_bytes+$datum->free_out_bytes)/1000**3, 2),
            ]);
        }
        return $formattedData;
    }

    /**
     * Convert bits to a specific SI suffix and return the power to divide by
     * @param $value
     * @return array
     */
    private function returnSiSuffixAndPower($value)
    {
        $power = 0;
        $suffixes = [
            'bps',
            'kbps', //kilo
            'Mbps', //mega
            'Gbps', //giga
            'Tbps', //tera
            'Pbps', //peta
            'Ebps', //exa
            'Zbps', //zetta
            'Ybps', //yotta
        ];

        while ($value > 1000 && array_key_exists($power, $suffixes)) {
            $value = $value / 1000;
            $power++;
        }

        return [
            'power' => $power,
            'suffix' => $suffixes[$power]
        ];
    }

    /**
     * Clear necessary caches after top off purchase
     */
    private function clearTopOffCache()
    {
        //Bust this cache so we see the new cap
        Cache::tags("usage_based_billing_policy_details")->forget(get_user()->account_id);
        $billingController = new BillingController();
        $billingController->clearBillingCache();
    }
}
