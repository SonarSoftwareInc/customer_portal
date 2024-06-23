<?php

namespace App\Services\Csp\Policies;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policies\Basic;
use Spatie\Csp\Scheme;
use Spatie\Csp\Value;

class SonarCustomerPortalPolicy extends Basic
{
	public function configure()
	{
  		$this
            // Duplicated from `Basic` because nonce overrides
            // the `unsafe-inline` for styles (see below) and
            // there's no `removeNonceForDirective`.
            ->addDirective(Directive::BASE, Keyword::SELF)
            ->addDirective(Directive::CONNECT, Keyword::SELF)
            ->addDirective(Directive::DEFAULT, Keyword::SELF)
            ->addDirective(Directive::FORM_ACTION, Keyword::SELF)
            ->addDirective(Directive::IMG, [
                Keyword::SELF,
                Scheme::DATA,
                // 'https://example.com',
            ])
            ->addDirective(Directive::MEDIA, Keyword::SELF)
            ->addDirective(Directive::OBJECT, Keyword::NONE)
            ->addDirective(Directive::SCRIPT, Keyword::SELF)
            ->addDirective(Directive::STYLE, [
                Keyword::SELF,
                Scheme::DATA,
                'https://fonts.googleapis.com',
                'https://fonts.gstatic.com',
            ])
            ->addDirective(Directive::FONT, [
                Keyword::SELF,
                Scheme::DATA,
                'https://fonts.googleapis.com',
                'https://fonts.gstatic.com',
            ])
            ->addNonceForDirective(Directive::SCRIPT)

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
                'unsafe-inline', // Required for TinyMCE https://www.tiny.cloud/docs/tinymce/6/tinymce-and-csp/
			])

            ->addDirective(Directive::CONNECT, [
                'self',
                'api.stripe.com',
            ]);
    }
}
