<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ContractController extends Controller
{
    private $apiController;
    public function __construct()
    {
        $this->apiController = new \SonarSoftware\CustomerPortalFramework\Controllers\ContractController();
    }

    public function index()
    {
        /**
         * This is not cached, as signing a contract outside the portal cannot be detected, and so would create invalid information display here.
         */
        $contracts = $this->apiController->getContracts(get_user()->account_id, 1);
        return view("pages.contracts.index", compact('contracts'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function downloadContractPdf($id)
    {
        $base64 = $this->apiController->getSignedContractAsBase64(get_user()->account_id, $id);

        return response()->make(base64_decode($base64), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=contract.pdf",
        ]);
    }
}
