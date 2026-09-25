@extends('layouts.full')
@section('content')
<div class="container-fluid">
   <div class="row justify-content-center">
      <div class="col-12">
         <!-- Header -->
         <div class="header mt-md-5">
            <div class="header-body">
               <div class="row align-items-center">
                  <div class="col">
                     <!-- Pretitle -->
                     <h6 class="header-pretitle">
                        {{utrans("billing.makePayment")}}
                     </h6>
                     <!-- Title -->
                     <h1 class="header-title">
                        {{utrans("billing.billing")}}
                     </h1>
                  </div>
                  <div class="col-auto">
                  </div>
               </div>
               <!-- / .row -->
            </div>
         </div>

         <!-- Bill Summary -->
         <div class="row">
            <div class="col-12 col-md-6">
               <div class="card">
                  <div class="table-responsive">
                     <table class="table table-sm card-table">
                        <thead>
                           <tr>
                              <th>{{ utrans('billing.totalAmountDue') }} {{ isset($additionalPaymentInformation['transaction_currency']) ? '(' . $additionalPaymentInformation['transaction_currency'] . ')' : '' }}</th>
                              <th>{{ utrans('billing.nextBillDate') }}</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td>{{ Formatter::currency($billingDetails->balance_due) }}</td>
                              <td>{{ Formatter::date($billingDetails->next_bill_date, false) }}</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>

         {!! Form::open(['action' => '\App\Http\Controllers\BillingController@submitTokenizedPayment', 'id' => 'paymentForm', 'class' => 'mb-4', 'autocomplete' => 'on']) !!}
         <div class="row mt-4">
         <div class="col-12">
               <div class="form-group">
                  <label>
                     {{utrans("billing.paymentMethod")}}
                  </label>
                  <select id="payment_method" name="payment_method" class="form-control">
                     @php
                     // Get the first key of the $paymentMethods array
                     $selectedPaymentMethod = array_key_first($paymentMethods);
                     @endphp
                     @foreach($paymentMethods as $id => $method)
                     <option value="{{ $id }}" data-type="{{ $method['type'] }}" {{ $id == $selectedPaymentMethod ? 'selected' : '' }}>
                        {{ $method['label'] }}
                     </option>
                     @endforeach
                  </select>
               </div>
            </div>
            <div class="col-12 ">
               <!-- Last name -->
               <div class="form-group new_card">
                  <!-- Label -->
                  <label>
                     {{utrans("billing.nameOnCard")}}
                  </label>
                  <!-- Input -->
                  {!! Form::text("name",null,['id' => 'name', 'class' => 'form-control', 'placeholder' => utrans("billing.nameOnCard")]) !!}
               </div>
            </div>

            <!-- Stripe Elements  -->

            <div class="col-lg-12 col-12 new_card">
               <div class="form-group" id="stripe_container" data-secret="{{ $secret }}" data-key="{{ $key }}">
                  <label for="name">Card</label>
                  <div id="card-element" class="form-control" style='height: 2.4em; padding-top: .7em;'></div>
                  <label id="stripe_errors" class="help-block error-help-block"></label>
               </div>
            </div>

            <div class="col-12">
               <div class="form-group new_card">
                  <label>
                     {{utrans("billing.line1")}}
                  </label>
                  {!! Form::text("line1",null,['id' => 'line1', 'class' => 'form-control', 'placeholder' => utrans("billing.line1--placeholder"), 'required' => true]) !!}
               </div>
            </div>
            <div class="col-12 col-md-6">
               <div class="form-group new_card">
                  <label>
                     {{utrans("billing.country")}}
                  </label>
                  {!! Form::select("country",countries(),config("customer_portal.country"),['id' => 'country', 'class' => 'form-control', 'required' => true]) !!}
               </div>
            </div>
            <div id="stateWrapper" class="col-12 col-md-6">
               <div class="form-group new_card">
                  <label>
                     {{utrans("billing.state")}}
                  </label>
                  {!! Form::select("state",subdivisions(config("customer_portal.country")),config("customer_portal.state"),['id' => 'state', 'class' => 'form-control', 'required' => true]) !!}
               </div>
            </div>
            <div class="col-12 col-md-6">
               <div class="form-group new_card">
                  <label>
                     {{utrans("billing.city")}}
                  </label>
                  {!! Form::text("city",null,['id' => 'city', 'class' => 'form-control', 'placeholder' => utrans("billing.city"), 'required' => true]) !!}
               </div>
            </div>
            <div class="col-12 col-md-6">
               <div class="form-group new_card">
                  <label>
                     {{utrans("billing.zip")}}
                  </label>
                  {!! Form::text("zip",null,['id' => 'zip', 'class' => 'form-control', 'placeholder' => utrans("billing.zip--placeholder"), 'required' => true]) !!}
               </div>
            </div>
            <div class="col-6 ">
               <div class="form-group">
                  <label>
                     {{ utrans("billing.totalAmountDue") }} {{ isset($additionalPaymentInformation['transaction_currency']) ? '(' . $additionalPaymentInformation['transaction_currency'] . ')' : '' }}
                  </label>
                  <div style="font-size: 1.5em;">
                     {{ Formatter::currency($billingDetails->balance_due) }}
                  </div>
               </div>
            </div>
            <div class="col-6 ">
               <div class="form-group">
                  <label>
                     {{utrans("billing.amountToPay")}} {{ isset($additionalPaymentInformation['transaction_currency']) ? '(' . $additionalPaymentInformation['transaction_currency'] . ')' : '' }}
                  </label>
                  {!! Form::number("amount",number_format($billingDetails->balance_due,2,".",""),['id' => 'amount', 'class' => 'form-control', 'step' => 'any', 'required' => true]) !!}
               </div>
            </div>
            <div class="col-auto new_card">
               <div class="custom-control custom-checkbox-toggle mt-1">
                  {!! Form::checkbox("makeAuto",1,false,['id' => 'makeAuto', 'class' => 'custom-control-input']) !!}
                  <label class="custom-control-label" for="makeAuto"></label>
               </div>
            </div>
            <div class="col mt-1 credit-card-autopay">
               {!! utrans("billing.saveAsAutoPayMethod") !!}
               {{utrans("billing.legalDisclaimer", ["business_name" => config("customer_portal.company_name")])}}
            </div>
            <div class="col mt-1 bank-account-payment">
               {{utrans("billing.authorizePaymentAccount", ["business_name" => config("customer_portal.company_name"), "dateToday" => $dateToday])}}
            </div>

            <!-- Additional payment Information -->
            @if(
               $additionalPaymentInformation['privacy_policy_link'] != '' || 
               $additionalPaymentInformation['return_refund_policy_link'] != '' || 
               $additionalPaymentInformation['delivery_policy_link'] != '' || 
               $additionalPaymentInformation['consumer_data_privacy_policy_link'] != '' || 
               $additionalPaymentInformation['secure_checkout_policy_link'] != '' || 
               $additionalPaymentInformation['terms_and_conditions_link'] != '' || 
               $additionalPaymentInformation['isp_name'] != '' || 
               $additionalPaymentInformation['company_address'] != '' || 
               $additionalPaymentInformation['customer_service_contact_phone'] != '' || 
               $additionalPaymentInformation['customer_service_contact_email'] != ''
            )
            <div class="col-12">
               <br>
               <!-- Policy Links -->
               @if($additionalPaymentInformation['privacy_policy_link'] != '' || $additionalPaymentInformation['return_refund_policy_link'] != '' || $additionalPaymentInformation['delivery_policy_link'] != '' || $additionalPaymentInformation['consumer_data_privacy_policy_link'] != '' || $additionalPaymentInformation['secure_checkout_policy_link'] != '' || $additionalPaymentInformation['terms_and_conditions_link'] != '')
               <p>
                  @if($additionalPaymentInformation['privacy_policy_link'] != '')
                  <a class="external" target="_blank" href="{{ $additionalPaymentInformation['privacy_policy_link'] }}">{{utrans("billing.privacy_policy")}}</a>@if(($additionalPaymentInformation['return_refund_policy_link'] != '' || $additionalPaymentInformation['delivery_policy_link'] != '' || $additionalPaymentInformation['consumer_data_privacy_policy_link'] != '' || $additionalPaymentInformation['secure_checkout_policy_link'] != '' || $additionalPaymentInformation['terms_and_conditions_link'] != '')), @endif
                  @endif
                  @if($additionalPaymentInformation['return_refund_policy_link'] != '')
                  <a class="external" target="_blank" href="{{ $additionalPaymentInformation['return_refund_policy_link'] }}">{{utrans("billing.refund_return_policy")}}</a>@if(($additionalPaymentInformation['delivery_policy_link'] != '' || $additionalPaymentInformation['consumer_data_privacy_policy_link'] != '' || $additionalPaymentInformation['secure_checkout_policy_link'] != '' || $additionalPaymentInformation['terms_and_conditions_link'] != '')), @endif
                  @endif
                  @if($additionalPaymentInformation['delivery_policy_link'] != '')
                  <a class="external" target="_blank" href="{{ $additionalPaymentInformation['delivery_policy_link'] }}">{{utrans("billing.delivery_policy")}}</a>@if(($additionalPaymentInformation['consumer_data_privacy_policy_link'] != '' || $additionalPaymentInformation['secure_checkout_policy_link'] != '' || $additionalPaymentInformation['terms_and_conditions_link'] != '')), @endif
                  @endif
                  @if($additionalPaymentInformation['consumer_data_privacy_policy_link'] != '')
                  <a class="external" target="_blank" href="{{ $additionalPaymentInformation['consumer_data_privacy_policy_link'] }}">{{utrans("billing.consumer_data_privacy_policy")}}</a>@if(($additionalPaymentInformation['secure_checkout_policy_link'] != '' || $additionalPaymentInformation['terms_and_conditions_link'] != '')), @endif
                  @endif
                  @if($additionalPaymentInformation['secure_checkout_policy_link'] != '')
                  <a class="external" target="_blank" href="{{ $additionalPaymentInformation['secure_checkout_policy_link'] }}">{{utrans("billing.secure_checkout_policy")}}</a>@if($additionalPaymentInformation['terms_and_conditions_link'] != ''), @endif
                  @endif
                  @if($additionalPaymentInformation['terms_and_conditions_link'] != '')
                  <a class="external" target="_blank" href="{{ $additionalPaymentInformation['terms_and_conditions_link'] }}">{{utrans("billing.terms_of_service")}}</a>
                  @endif
               </p>
               @endif

               <!-- Company Info -->
               <p>
                  {{ $additionalPaymentInformation['isp_name'] ?? '' }}{{ $additionalPaymentInformation['isp_name'] != '' ? ':' : '' }} {{ $additionalPaymentInformation['company_address'] }}
                  @if($additionalPaymentInformation['customer_service_contact_phone'] != '')
                  {{ utrans("billing.contact_us") }}:
                  <a class="external" href="tel:{{ $additionalPaymentInformation['customer_service_contact_phone'] }}">
                     {{ $additionalPaymentInformation['customer_service_contact_phone'] }}
                  </a>
                  @endif
                  @if($additionalPaymentInformation['customer_service_contact_email'] != '')
                     @if($additionalPaymentInformation['customer_service_contact_phone'] != '')
                        ,
                     @endif
                     <a class="external" href="mailto:{{ $additionalPaymentInformation['customer_service_contact_email'] }}">
                        {{ $additionalPaymentInformation['customer_service_contact_email'] }}
                     </a>
                  @endif
               </p>
            </div>
            @endif
            <div class="col-12 col-md-12 mt-5">
               <input type="hidden" name="payment_tracker_id" value="{{uniqid("", true)}}" />
               <button id="submit_payment" type="submit" class="btn btn-primary">{{utrans("billing.submitPayment")}}</button>
            </div>
            {!! Form::close() !!}
         </div>
      </div>
      <!-- / .row -->
   </div>
   <!-- / .container-fluid -->
</div>
<!-- / .main-content -->
@endsection
@section('additionalJS')
<script src="/assets/libs/jquery-payment-plugin/jquery.payment.min.js"></script>
<script src="/assets/js/pages/billing/payment/page_stripe.js"></script>
<script type="text/javascript" src="/assets/libs/js-validation/jsvalidation.min.js"></script>
{!! JsValidator::formRequest('App\Http\Requests\TokenizedCreditCardPaymentRequest','#paymentForm') !!}
@endsection