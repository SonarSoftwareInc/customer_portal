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




/*

add_header Content-Security-Policy 
"frame-src 'self' https://js.stripe.com; 
frame-ancestors 'none'; 
form-action 'self' https://www.paypal.com; 
upgrade-insecure-requests; 
script-src 'self' 'sha256-8MEQ/Qvo0Y09Vo5TDuyuOW39tu8QgAkymm2kKnkZ4iU=' 'sha256-hCdV2+S+9aRKKJlfK5CGe8NOfdvwBm9EvUlaeGXu0rE=' 'sha256-gzorWt76ec20Vfh2hf2HnxowkJXaHEJ2HinEBjvK6X4=' https://js.stripe.com; 
connect-src 'self' https://api.stripe.com;";


 const BASE = 'base-uri';
    const BLOCK_ALL_MIXED_CONTENT = 'block-all-mixed-content';
    const CHILD = 'child-src';
    const CONNECT = 'connect-src';
    const DEFAULT = 'default-src';
    const FONT = 'font-src';
    const FORM_ACTION = 'form-action';
    const FRAME = 'frame-src';
    const FRAME_ANCESTORS = 'frame-ancestors';
    const IMG = 'img-src';
    const MANIFEST = 'manifest-src';
    const MEDIA = 'media-src';
    const OBJECT = 'object-src';
    const PLUGIN = 'plugin-types';
    const PREFETCH = 'prefetch-src';
    const REPORT = 'report-uri';
    const REPORT_TO = 'report-to';
    const SANDBOX = 'sandbox';
    const SCRIPT = 'script-src';
    const SCRIPT_ATTR = 'script-src-attr';
    const SCRIPT_ELEM = 'script-src-elem';
    const STYLE = 'style-src';
    const STYLE_ATTR = 'style-src-attr';
    const STYLE_ELEM = 'style-src-elem';
    const UPGRADE_INSECURE_REQUESTS = 'upgrade-insecure-requests';
    const WEB_RTC = 'webrtc-src';
    const WORKER = 'worker-src';



*/
