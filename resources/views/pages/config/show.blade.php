@extends('layouts.no_nav')
@section('content')
<div class="container-fluid">
<div class="row justify-content-center">
   <div class="col-lg-8 col-md-8 col-12">
      <div class="header mt-md-5">
         <div class="header-body">
            <div class="row align-items-center">
               <div class="col">
                  <h1 class="header-title">
                     App Configuration
                  </h1>
               </div>
            </div>
         </div>
      </div>
      <div class="card">
         <div class="card-header">
            <h4 class="card-header-title text-muted">
               <i class="fe fe-settings mr-3"></i> System Settings
            </h4>
         </div>
         <div class="card-body">
            {!! Form::open(['id' => 'configForm', 'files' => true, 'class' => 'mb-4', 'autocomplete' => 'on']) !!}
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Application URL
                     </label>
                     {!! Form::text("url", $systemSetting->url ,['id' => 'url', 'class' => 'form-control', 'placeholder' => "https://myisp.org", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-original-title' => 'Set the URL of your customer portal (eg. https://myisp.org)']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1 mb--5">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Locale
                     </label>
                     {!! Form::select('locale', getAvailableLanguages(), $systemSetting->locale, ['id' => 'locale', 'class' => 'form-control', 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-original-title' => 'The default two-letter language identifier to be used by your customer portal, eg. en']) !!}
                  </div>
               </div>
            </div>
         </div>
      </div>
     <div class="card">
         <div class="card-header">
            <h4 class="card-header-title text-muted">
               <i class="fe fe-cast mr-3"></i> Design Settings
            </h4>
         </div>
         <div class="card-body">
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Logo
                     </label> <br>
                    {!! Form::file('image', null, ['class' => 'form-control']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Branding Cover
                     </label> <br>
                    {!! Form::file('cover', null, ['class' => 'form-control']) !!}
                  </div>
               </div>
            </div>
            {{--<div class="row mt-1">--}}
               {{--<div class="col-12 mb--3 ">--}}
                  {{--<div class="form-group">--}}
                     {{--<label>--}}
                     {{--Primary Color--}}
                     {{--</label>--}}
                  {{--</div>--}}
               {{--</div>--}}
            {{--</div>--}}
            {{--<div class="row">--}}
            {{--<div class="col-3">--}}
            {{--<input id="primaryColor" type="text" class="form-control" value="pink" />--}}
            {{--</div>--}}
            {{--</div>--}}
         </div>
      </div>


      <div class="card">
         <div class="card-header">
            <h4 class="card-header-title text-muted">
               <i class="fe fe-mail mr-3"></i> Mail Settings
            </h4>
         </div>
         <div class="card-body">
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Mail Host
                     </label>
                     {!! Form::text("mail_host",$systemSetting->mail_host,['id' => 'mail_host', 'class' => 'form-control', 'placeholder' => "mailtrap.io", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-original-title' => 'The hostname for your mail server']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Mail Port
                     </label>
                     <div class="input-group input-group-merge mb-3">
                        {!! Form::text("mail_port",$systemSetting->mail_port,['id' => 'mail_port', 'class' => 'form-control form-control-prepended', 'placeholder' => "2525", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-original-title' => 'The port that your mail server is listening on']) !!}
                     </div>
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Mail Username
                     </label>
                     {!! Form::text("mail_username",$systemSetting->mail_username,['id' => 'mail_username', 'class' => 'form-control', 'placeholder' => "", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-original-title' => 'The administrative username for your mail server']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Mail Password
                     </label>
                     {!! Form::text("mail_password",$systemSetting->mail_password, ['id' => 'mail_password', 'class' => 'form-control', 'placeholder' => "", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-original-title' => 'The administrative password for your mail server']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="form-group">
                  <div class="col-auto ">
                     <!-- Toggle -->
                     <div class="custom-control custom-checkbox-toggle mt-1">
                        {{ Form::hidden('mail_encryption',0) }}
                        {!! Form::checkbox("mail_encryption",1,$systemSetting->mail_encryption,['id' => 'mail_encryption', 'class' => 'custom-control-input', 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-original-title' => 'If your mail server utilizes TLS encryption, then leave this enabled']) !!}
                        <label class="custom-control-label" for="mail_encryption"></label>
                     </div>
                  </div>
               </div>
               <div class="col-auto mt-2">
                  <label>
                  Encryption (TLS)
                  </label>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Sender Email
                     </label>
                     {!! Form::email("mail_from_address",$systemSetting->mail_from_address,['id' => 'mail_from_address', 'class' => 'form-control', 'placeholder' => "donotreply@ispportal.net", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-original-title' => 'The email address that your customers will recieve automated messages from']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Sender Name
                     </label>
                     {!! Form::text("mail_from_name",$systemSetting->mail_from_name,['id' => 'mail_from_name', 'class' => 'form-control', 'placeholder' => "ISP", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-original-title' => 'The name that will show up on the Sender Email address']) !!}
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card">
         <div class="card-header">
            <h4 class="card-header-title text-muted">
               <i class="fe fe-settings mr-3"></i> General Config
            </h4>
         </div>
         <div class="card-body">
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     ISP Name
                     </label>
                     {!! Form::text("isp_name",$systemSetting->isp_name,['id' => 'isp_name', 'class' => 'form-control', 'placeholder' => "islandNet", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-original-title' => 'Your company name, to be used throughout the customer portal']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Decimal Seperator
                     </label>
                     {!! Form::text("decimal_separator",$systemSetting->decimal_separator,['id' => 'decimal_separator', 'class' => 'form-control', 'placeholder' => ".", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'The symbol to be used as a decimal, eg $34<strong>.</strong>95']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Thousands Seperator
                     </label>
                     <div class="input-group mb-3">
                        {!! Form::text("thousands_separator",$systemSetting->thousands_separator,['id' => 'thousands_separator', 'class' => 'form-control', 'placeholder' => ",", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'The symbol to be used as a comma, eg $3<strong>,</strong>953']) !!}
                     </div>
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Currency Symbol
                     </label>
                     {!! Form::text("currency_symbol",$systemSetting->currency_symbol,['id' => 'currency_symbol', 'class' => 'form-control', 'placeholder' => "$", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'The symbol used to show currency, eg <strong>$</strong>100']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Country
                     </label>
                     {!! Form::select("country",countries(), $systemSetting->country,['id' => 'country', 'class' => 'form-control', 'placeholder' => "US", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'Your valid, two-letter country code (eg. US)']) !!}
                  </div>
               </div>
            </div>
			@if(count(subdivisions(\Illuminate\Support\Facades\Config::get("customer_portal.country"))) != 0)
            <div id="stateWrapper" class="row mt-1">
               <div class="col-12">
                  <div class="form-group">
                     <label>
                     State
                     </label>
                     {!! Form::select("state",subdivisions(\Illuminate\Support\Facades\Config::get("customer_portal.country")),\Illuminate\Support\Facades\Config::get("customer_portal.state"),['id' => 'state', 'class' => 'form-control state-control']) !!}
				</div>
               </div>
            </div>
			@endif
            <div class="row mt-1">
               <div class="col-12 ">
                  <div class="form-group">
                     <label>
                     Login Page Message
                     </label>
                     {!! Form::text("login_page_message",$systemSetting->login_page_message,['id' => 'login_page_message', 'class' => 'form-control', 'placeholder' => "", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'The message shown to customers on the login page']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="form-group">
                  <div class="col-auto ">
                     <!-- Toggle -->
                     <div class="custom-control custom-checkbox-toggle mt-1">
                        {{ Form::hidden('data_usage_enabled',0) }}
                        {!! Form::checkbox("data_usage_enabled",1,$systemSetting->data_usage_enabled,['id' => 'data_usage_enabled', 'class' => 'custom-control-input', 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'Enable this to utilize data usage functionality']) !!}
                        <label class="custom-control-label" for="data_usage_enabled"></label>
                     </div>
                  </div>
               </div>
               <div class="col-auto mt-2">
                  <label>
                  Enable Data Usage
                  </label>
               </div>
            </div>
            <div class="row mt-1">
               <div class="form-group">
                  <div class="col-auto ">
                     <!-- Toggle -->
                     <div class="custom-control custom-checkbox-toggle mt-1">
                        {{ Form::hidden('contracts_enabled',0) }}
                        {!! Form::checkbox("contracts_enabled",1,$systemSetting->contracts_enabled,['id' => 'contracts_enabled', 'class' => 'custom-control-input', 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'Enable this to utilize contract functionality']) !!}
                        <label class="custom-control-label" for="contracts_enabled"></label>
                     </div>
                  </div>
               </div>
               <div class="col-auto mt-2">
                  <label>
                  Enable Contracts
                  </label>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12">
                  <div class="form-group">
                     <label>
                     Required Password Strength
                     </label>
                     {!! Form::select('password_strength_required', array('1' => 'Minimal', '2' => 'Low', '3' => 'Moderate', '4' => 'High', '5' => 'Maximum'), $systemSetting->password_strength_required,['id' => 'password_strength_required', 'class' => 'form-control', 'data-toggle' => 'select', 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'The password strength standard required for new users and password resets']); !!}
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card">
         <div class="card-header">
            <h4 class="card-header-title text-muted">
               <i class="fe fe-settings mr-3"></i> General Billing
            </h4>
         </div>
         <div class="card-body">
            <div class="row mt-1">
               <div class="form-group">
                  <div class="col-auto ">
                     <!-- Toggle -->
                     <div class="custom-control custom-checkbox-toggle mt-1">
                        {!! Form::hidden('show_detailed_transactions', 0) !!}
                        {!! Form::checkbox("show_detailed_transactions",1,$systemSetting->show_detailed_transactions,['id' => 'show_detailed_transactions', 'class' => 'custom-control-input', 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'Show detailed transaction information to your users']) !!}
                        <label class="custom-control-label" for="show_detailed_transactions"></label>
                     </div>
                  </div>
               </div>
               <div class="col-auto mt-2">
                  <label>
                  Show Detailed Transactions
                  </label>
               </div>
            </div>
            <div class="row mt-1">
               <div class="form-group">
                  <div class="col-auto ">
                     <!-- Toggle -->
                     <div class="custom-control custom-checkbox-toggle mt-1">
                        {{ Form::hidden('credit_card_payments_enabled',0) }}
                        {!! Form::checkbox("credit_card_payments_enabled",1,$systemSetting->credit_card_payments_enabled,['id' => 'credit_card_payments_enabled', 'class' => 'custom-control-input', 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'Enable credit card payment functionality']) !!}
                        <label class="custom-control-label" for="credit_card_payments_enabled"></label>
                     </div>
                  </div>
               </div>
               <div class="col-auto mt-2">
                  <label>
                  Enable Credit Card Payments
                  </label>
               </div>
            </div>
            <div class="row mt-1">
               <div class="form-group">
                  <div class="col-auto ">
                     <!-- Toggle -->
                     <div class="custom-control custom-checkbox-toggle mt-1">
                        {{ Form::hidden('bank_payments_enabled',0) }}
                        {!! Form::checkbox("bank_payments_enabled",1,$systemSetting->bank_payments_enabled,['id' => 'bank_payments_enabled', 'class' => 'custom-control-input', 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'Enable bank payment functionality']) !!}
                        <label class="custom-control-label" for="bank_payments_enabled"></label>
                     </div>
                  </div>
               </div>
               <div class="col-auto mt-2">
                  <label>
                  Enable Bank Payments
                  </label>
               </div>
            </div>
         </div>
      </div>
      <div class="card">
         <div class="card-header">
            <h4 class="card-header-title text-muted">
               <i class="fe fe-credit-card mr-3"></i> GoCardless Integration
            </h4>
         </div>
         <div class="card-body">
            <div class="row mt-1">
               <div class="form-group">
                  <div class="col-auto ">
                     <!-- Toggle -->
                     <div class="custom-control custom-checkbox-toggle mt-1">
                        {{ Form::hidden('go_cardless_enabled',0) }}
                        {!! Form::checkbox("go_cardless_enabled",1,$systemSetting->go_cardless_enabled,['id' => 'go_cardless_enabled', 'class' => 'custom-control-input', 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'Integrate GoCardless into your billing flow']) !!}
                        <label class="custom-control-label" for="go_cardless_enabled"></label>
                     </div>
                  </div>
               </div>
               <div class="col-auto mt-2">
                  <label>
                  Enable GoCardless
                  </label>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12">
                  <div class="form-group">
                     <label>
                     API Token
                     </label>
                     {!! Form::text("go_cardless_access_token",$systemSetting->go_cardless_access_token,['id' => 'go_cardless_access_token', 'class' => 'form-control', 'placeholder' => "", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'Your merchant API token provided by GoCardless']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12">
                  <div class="form-group">
                     <label>
                     Currency Mode
                     </label>
                     {!! Form::select('go_cardless_currency_code', array('AUD' => 'AUD', 'CAD' => 'CAD', 'DKK' => 'DKK', 'EUR' => 'EUR', 'GBP' => 'GBP', 'NZD' => 'NZD', 'SEK' => 'SEK', 'USD' => 'USD'), $systemSetting->go_cardless_currency_code,['id' => 'go_cardless_currency_code', 'class' => 'form-control', 'data-toggle' => 'select']); !!}
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card">
         <div class="card-header">
            <h4 class="card-header-title text-muted">
               <i class="fe fe-credit-card mr-3"></i> PayPal Integration
            </h4>
         </div>
         <div class="card-body">
            <div class="row mt-1">
               <div class="form-group">
                  <div class="col-auto ">
                     <!-- Toggle -->
                     <div class="custom-control custom-checkbox-toggle mt-1">
                        {{ Form::hidden('paypal_enabled',0) }}
                        {!! Form::checkbox("paypal_enabled",1,$systemSetting->paypal_enabled,['id' => 'paypal_enabled', 'class' => 'custom-control-input', 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'Enable or disable PayPal integration in your payment flow']) !!}
                        <label class="custom-control-label" for="paypal_enabled"></label>
                     </div>
                  </div>
               </div>
               <div class="col-auto mt-2">
                  <label>
                  Enable PayPal
                  </label>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12">
                  <div class="form-group">
                     <label>
                     API Client ID
                     </label>
                     {!! Form::text("paypal_api_client",$systemSetting->paypal_api_client,['id' => 'paypal_api_client', 'class' => 'form-control', 'placeholder' => "", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'Your merchant API ID provided by PayPal']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12">
                  <div class="form-group">
                     <label>
                     API Client Secret
                     </label>
                     {!! Form::text("paypal_api_client_secret",$systemSetting->paypal_api_client_secret,['id' => 'paypal_api_client_secret', 'class' => 'form-control', 'placeholder' => "", 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'Your merchant API secret provided by PayPal']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12">
                  <div class="form-group">
                     <label>
                     PayPal Currency
                     </label>
                     {!! Form::select('paypal_currency_code', $paypalCurrency, $systemSetting->paypal_currency_code,['id' => 'paypal_currency_code', 'class' => 'form-control', 'data-toggle' => 'select', 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'The currency mode your PayPal account is configured for']); !!}
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card">
         <div class="card-header">
            <h4 class="card-header-title text-muted">
               <i class="fe fe-message-circle mr-3"></i> Tickets
            </h4>
         </div>
         <div class="card-body">
            <div class="row mt-1">
               <div class="form-group">
                  <div class="col-auto ">
                     <!-- Toggle -->
                     <div class="custom-control custom-checkbox-toggle mt-1">
                        {{ Form::hidden('ticketing_enabled',0) }}
                        {!! Form::checkbox("ticketing_enabled",1,$systemSetting->ticketing_enabled,['id' => 'ticketing_enabled', 'class' => 'custom-control-input', 'data-toggle' => 'tooltip', 'data-trigger' => 'hover','data-placement' => 'left','data-offset' => '3','data-html' => 'true', 'data-original-title' => 'Enable tickets in your customer portal']) !!}
                        <label class="custom-control-label" for="ticketing_enabled"></label>
                     </div>
                  </div>
               </div>
               <div class="col-auto mt-2">
                  <label>
                  Enable Ticketing
                  </label>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12">
                  <div class="form-group">
                     <label>
                     Inbound Email
                     </label>
                     {!! Form::select("inbound_email_account_id", $inboundEmailAccounts, $systemSetting->inbound_email_account_id, ['id' => 'inbound_email_account_id', 'class' => 'form-control']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12">
                  <div class="form-group">
                     <label>
                     Ticket Group
                     </label>
                     {!! Form::select("ticket_group_id", $ticketGroups, $systemSetting->ticket_group_id,['id' => 'ticket_group_id', 'class' => 'form-control']) !!}
                  </div>
               </div>
            </div>
            <div class="row mt-1">
               <div class="col-12">
                  <div class="form-group">
                     <label>
                     Ticket Priority
                     </label>
                     {!! Form::select("ticket_priority",[1 => 'Critical', 2 => 'High', 3 => 'Medium', 4 => 'Low'],$systemSetting->ticket_priority, ['id' => 'ticket_priority', 'class' => 'form-control']) !!}
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="row mt-1 mb-4">
         <div class="col-12">
            <button type="submit" class="btn btn-primary">
            Save Changes
            </button>
            {!! Form::close() !!}
         </div>
      </div>
   </div>
</div>
@endsection
@section('additionalJS')
<script src="/assets/libs/jquery-payment-plugin/jquery.payment.min.js"></script>
<script src="/assets/libs/bootstrap-colorpicker-plugin/bootstrap-colorpicker.min.js"></script>
<script src="/assets/js/pages/settings/settings.js"></script>

<script>
   $(function () {
     $('[data-toggle="tooltip"]').tooltip()
   })
    $(function() {
        $('#primaryColor').colorpicker({
            customClass: 'colorpicker-2x',
            sliders: {
                saturation: {
                    maxLeft: 200,
                    maxTop: 200
                },
                hue: {
                    maxTop: 200
                },
                alpha: {
                    maxTop: 200
                }
            }
        });
    });
</script>
@endsection
