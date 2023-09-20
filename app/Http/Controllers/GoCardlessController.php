<?php

namespace App\Http\Controllers;

use App\Billing\GoCardless;
use App\GoCardlessToken;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class GoCardlessController extends Controller
{
    public function handleReturnRedirect(Request $request): RedirectResponse
    {
        $gocardlessToken = GoCardlessToken::where('redirect_flow_id', '=', $request->input('redirect_flow_id'))
            ->where('account_id', '=', get_user()->account_id)
            ->first();
        if (! $gocardlessToken) {
            return redirect()->action([\App\Http\Controllers\BillingController::class, 'index'])->withErrors(utrans('errors.invalidRedirectFlowID'));
        }

        try {
            $gocardless = new GoCardless();
            $result = $gocardless->completeRedirect($gocardlessToken);
            $controller = new BillingController();
            $controller->clearBillingCache();

            return Redirect::away($result);
        } catch (Exception $e) {
            return redirect()->action([\App\Http\Controllers\BillingController::class, 'index'])->withErrors(utrans('errors.invalidRedirectFlowID'));
        }
    }
}
