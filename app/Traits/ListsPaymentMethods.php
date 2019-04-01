<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use SonarSoftware\CustomerPortalFramework\Controllers\AccountBillingController;

trait ListsPaymentMethods
{
    /**
     * Get all the payment method options
     * @return mixed
     */
    private function getPaymentMethods()
    {
        $accountBillingController = new AccountBillingController();
        if (!Cache::tags("billing.payment_methods")->has(get_user()->account_id)) {
            $validAccountMethods = $accountBillingController->getValidPaymentMethods(get_user()->account_id);
            Cache::tags("billing.payment_methods")->put(get_user()->account_id, $validAccountMethods, 10);
        }
        return Cache::tags("billing.payment_methods")->get(get_user()->account_id);
    }
}
