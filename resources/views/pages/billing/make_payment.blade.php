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

         {!! Form::open(['action' => '\App\Http\Controllers\BillingController@submitPayment','id' => 'paymentForm','class' => 'mb-4','autocomplete' => 'on']) !!}

         <!-- Expandable invoices table  -->
         @if($invoices != [])
         <div class=" row">
            <div class="col-12 col-md-8">
               <div class="card">
                  <div class="card-header">
                     <h4 class="card-header-title text-muted">
                        <i class="fe fe-inbox mr-3"></i>{{utrans("headers.invoices")}}
                     </h4>
                     <span id="toggleInvoices" class="btn btn-sm btn-primary float-right" style="flex: none;">
                        <span class="show-text"> {{utrans("billing.show")}} </span>
                        <span class="hide-text" style="display: none;"> {{utrans("billing.hide")}} </span>
                     </span>
                  </div>
                  <div class="table-responsive invoicesTable ">
                     <table class="table table-sm card-table">
                        <thead>
                           <tr>
                              <th>
                                 {{utrans("billing.date")}}
                              </th>
                              <th>
                                 {{utrans("billing.invoiceNumber")}}
                              </th>
                              <th>
                                 {{utrans("billing.remainingDue")}}
                              </th>
                              <th>
                                 {{utrans("billing.dueDate")}}
                              </th>
                              <th>
                                 {{utrans("billing.viewInvoice")}}
                              </th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($invoices as $invoice)
                           <tr>
                              <td>
                                 {{ Formatter::date($invoice->date, false) }}
                              </td>
                              <td>
                                 {{ $invoice->id }}
                              </td>
                              <td>
                                 {{ Formatter::currency($invoice->remaining_due) }}
                              </td>
                              <td>
                                 {{ Formatter::date($invoice->due_date, false) }}
                              </td>
                              <td>
                                 <a class="btn btn-sm" href="{{action([\App\Http\Controllers\BillingController::class, 'getInvoicePdf'],['invoices' => $invoice->id])}}" role="button">
                                    <i class="fe fe-file-text mr-1"></i>
                                    {{utrans("billing.downloadInvoice")}}
                                 </a>
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
         @endif

         <!-- Form -->
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
               <div class="form-group new_card">
                  <label>
                     {{utrans("billing.nameOnCard")}}
                  </label>
                  {!! Form::text("name",null,['id' => 'name', 'class' => 'form-control', 'placeholder' => utrans("billing.nameOnCard--placeholder")]) !!}
               </div>
            </div>
            <div class="col-12 credit-card-images">
               @if(!empty($enabledPrimaryCreditCardProcessor))
                  @if($enabledPrimaryCreditCardProcessor->visa)
                  <img src="/assets/svg/creditcards/visa.svg" alt="Visa" class="credit-card-image" style="width: 35px;">
                  @endif
                  @if($enabledPrimaryCreditCardProcessor->mastercard)
                  <img src="/assets/svg/creditcards/mastercard.svg" alt="MasterCard" class="credit-card-image" style="width: 35px;">
                  @endif
                  @if($enabledPrimaryCreditCardProcessor->amex)
                  <img src="/assets/svg/creditcards/amex.svg" alt="American Express" class="credit-card-image" style="width: 35px;">
                  @endif
                  @if($enabledPrimaryCreditCardProcessor->discover)
                  <img src="/assets/svg/creditcards/discover.svg" alt="Discover" class="credit-card-image" style="width: 35px;">
                  @endif
               @endif
            </div>
            <div class="col-12 col-md-4">
               <div class="form-group new_card">
                  <label class="cc_number">
                     {{utrans("billing.creditCardNumber")}}
                  </label>
                  {!! Form::tel("cc-number",null,['id' => 'cc-number', 'autocomplete' => 'cc-number', 'class' => 'cc-number form-control', 'placeholder' => utrans("billing.creditCardNumber--placeholder")]) !!}
               </div>
            </div>
            <div class="col-12 col-md-4">
               <div class="form-group new_card">
                  <label>
                     {{utrans("billing.expirationDate")}}
                  </label>
                  {!! Form::tel("expirationDate",null,['id' => 'expirationDate', 'class' => 'form-control', 'placeholder' => utrans("billing.expirationDate--placeholder")]) !!}
               </div>
            </div>
            <div class="col-12 col-md-4">
               <div class="form-group new_card">
                  <label>
                     {{utrans("billing.cvc")}}
                  </label>
                  {!! Form::tel("cvc",null,['id' => 'cvc', 'autocomplete' => 'cvc', 'class' => 'form-control', 'placeholder' => utrans("billing.cvc--placeholder")]) !!}
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
                  {!! Form::select("country", countries(), old('country', config("customer_portal.country")), ['id' => 'country', 'class' => 'form-control', 'required' => true]) !!}
               </div>
            </div>
            <div id="stateWrapper" class="col-12 col-md-6">
               <div class="form-group new_card">
                  <label>
                     {{utrans("billing.state")}}
                  </label>
                  {!! Form::select("state", subdivisions(old('country', config("customer_portal.country"))), old('state', config("customer_portal.state")), ['id' => 'state', 'class' => 'form-control', 'required' => true]) !!}
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
         </div>
         {!! Form::close() !!}
      </div>
      <!-- / .row -->
   </div>
   <!-- / .container-fluid -->
</div>
<!-- / .main-content -->
@endsection
@section('additionalJS')
<script src="/assets/libs/jquery-payment-plugin/jquery.payment.min.js"></script>
<script src="/assets/js/pages/billing/payment/page.js"></script>
<script type="text/javascript" src="/assets/libs/js-validation/jsvalidation.min.js"></script>
{!! JsValidator::formRequest('App\Http\Requests\CreditCardPaymentRequest','#paymentForm') !!}
@endsection