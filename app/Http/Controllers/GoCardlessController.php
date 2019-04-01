<?php

namespace App\Http\Controllers;

use App\Billing\GoCardless;
use App\GoCardlessToken;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class GoCardlessController extends Controller
{
    /**
     * @param Request $request
     * @return $this
     */
    public function handleReturnRedirect(Request $request)
    {
        $gocardlessToken = GoCardlessToken::where('redirect_flow_id','=',$request->input('redirect_flow_id'))
            ->where('account_id','=',get_user()->account_id)
            ->first();
        if (!$gocardlessToken)
        {
            return redirect()->action("BillingController@index")->withErrors(utrans("errors.invalidRedirectFlowID"));
        }

        try {
            $gocardless = new GoCardless();
            $result = $gocardless->completeRedirect($gocardlessToken);
            $controller = new BillingController();
            $controller->clearBillingCache();
            return Redirect::away($result);
        }
        catch (Exception $e)
        {
            return redirect()->action("BillingController@index")->withErrors(utrans("errors.invalidRedirectFlowID"));
        }
    }
}
