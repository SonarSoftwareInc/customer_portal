<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddTopOffRequest;
use App\Traits\ListsPaymentMethods;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Factory;
use Illuminate\View\View;
use SonarSoftware\CustomerPortalFramework\Controllers\DataUsageController as FrameworkDataUsageController;

class DataUsageController extends Controller
{
    use ListsPaymentMethods;

    private FrameworkDataUsageController $frameworkDataUsageController;

    public function __construct()
    {
        $this->frameworkDataUsageController = new FrameworkDataUsageController();
    }

    public function index(): Factory|View
    {
        $historicalUsage = $this->getHistoricalUsage();
        $policyDetails = $this->getPolicyDetails();
        $currentUsage = $historicalUsage ? $historicalUsage[0] : [];
        $historicalUsage = json_encode($historicalUsage);
        $calculatedCap = $policyDetails->policy_cap_in_gigabytes
            + round($policyDetails->rollover_available_in_bytes / 1000 ** 3, 2)
            + round($policyDetails->purchased_top_off_total_in_bytes / 1000 ** 3, 2);
        if ($calculatedCap > 0) {
            $usagePercentage = round(($currentUsage['billable'] / $calculatedCap) * 100);
        } else {
            $usagePercentage = 0;
        }

        return view(
            'pages.data_usage.index',
            compact('historicalUsage', 'policyDetails', 'currentUsage', 'calculatedCap', 'usagePercentage')
        );
    }

    /**
     * Show the top off purchase page
     */
    public function showTopOff(): Factory|View|RedirectResponse
    {
        $policyDetails = $this->getPolicyDetails();
        if ($policyDetails->allow_user_to_purchase_capacity !== true) {
            return redirect()->back()->withErrors(utrans('errors.topOffNotAvailable'));
        }

        return view('pages.data_usage.add_top_off', compact('policyDetails'));
    }

    /**
     * Endpoint to add additional capacity if immediate payment is not required
     */
    public function addTopOff(AddTopOffRequest $request): RedirectResponse
    {
        $policyDetails = $this->getPolicyDetails();
        if ($policyDetails->allow_user_to_purchase_capacity !== true) {
            return redirect()->back()->withErrors(utrans('errors.topOffNotAvailable'));
        }

        try {
            $this->frameworkDataUsageController->purchaseTopOff(get_user()->account_id, $request->input('quantity'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(utrans('errors.failedToAddDataUsage'));
        }

        $this->clearTopOffCache();

        return redirect()
            ->action([\App\Http\Controllers\DataUsageController::class, 'index'])
            ->with('success', utrans('data_usage.successfullyAddedUsage'));
    }

    /**
     * Get cached usage based billing policy details. Cache is lower on this one in case service is changed.
     *
     * @return mixed
     */
    private function getPolicyDetails()
    {
        if (! Cache::tags('usage_based_billing_policy_details')->has(get_user()->account_id)) {
            $policyDetails = $this->frameworkDataUsageController->getUsageBasedBillingPolicyDetails(
                get_user()->account_id
            );
            Cache::tags('usage_based_billing_policy_details')->put(
                get_user()->account_id,
                $policyDetails,
                Carbon::now()->addMinutes(10)
            );
        }

        return Cache::tags('usage_based_billing_policy_details')->get(get_user()->account_id);
    }

    /**
     * Get cached historical data usage
     *
     * @return mixed
     */
    private function getHistoricalUsage()
    {
        if (! Cache::tags('historical_data_usage')->has(get_user()->account_id)) {
            $dataUsage = $this->formatHistoricalUsageData(
                array_slice(
                    $this->frameworkDataUsageController->getAggregatedDataUsage(get_user()->account_id),
                    0,
                    12
                )
            );
            Cache::tags('historical_data_usage')->put(
                get_user()->account_id,
                $dataUsage,
                Carbon::now()->addMinutes(60)
            );
        }

        return Cache::tags('historical_data_usage')->get(get_user()->account_id);
    }

    /**
     * Convert all historical usage to gigabytes
     */
    private function formatHistoricalUsageData($historicalUsageData): array
    {
        $formattedData = [];
        foreach ($historicalUsageData as $datum) {
            $timestamp = new Carbon($datum->start_time, 'UTC');
            array_push($formattedData, [
                'timestamp' => $timestamp->toRfc3339String(),
                'billable' => round(($datum->billable_in_bytes + $datum->billable_out_bytes) / 1000 ** 3, 2),
                'free' => round(($datum->free_in_bytes + $datum->free_out_bytes) / 1000 ** 3, 2),
            ]);
        }

        return $formattedData;
    }

    /**
     * Clear necessary caches after top off purchase
     */
    private function clearTopOffCache()
    {
        //Bust this cache so we see the new cap
        Cache::tags('usage_based_billing_policy_details')->forget(get_user()->account_id);
        $billingController = new BillingController();
        $billingController->clearBillingCache();
    }
}
