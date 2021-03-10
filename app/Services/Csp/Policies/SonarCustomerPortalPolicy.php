<?php
namespace App\Services\Csp\Policies;

use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Basic;
use Spatie\Csp\Keyword;
use Spatie\Csp\Value;

class SonarCustomerPortalPolicy extends Basic
{
	public function configure()
	{
		parent::configure();


  		$this
            ->addDirective(Directive::FRAME, [
                'self',
                'js.stripe.com',
            ])

            ->addDirective(Directive::FRAME_ANCESTORS, Keyword::NONE)

            ->addDirective(Directive::FORM_ACTION, [
                'self',
                'www.paypal.com',
            ])

            ->addDirective(Directive::UPGRADE_INSECURE_REQUESTS, Value::NO_VALUE)

            ->addDirective(Directive::SCRIPT, [
                'self',
                'js.stripe.com',
            ])

			->addDirective(Directive::STYLE, [
				'self',
			])

            ->addDirective(Directive::CONNECT, [
                'self',
                'api.stripe.com',
            ]);

	}
}
