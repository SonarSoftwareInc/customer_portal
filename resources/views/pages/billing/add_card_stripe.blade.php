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
                        {{utrans("billing.addNewCard")}}
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
                              <th>{{ utrans('billing.nextBillAmount') }} {{ isset($transaction_currency) ? '(' . $transaction_currency . ')' : '' }}</th>
                              <th>{{ utrans('billing.nextBillDate') }}</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td>
                                 @if($next_bill_amount !== null)
                                 {{Formatter::currency($next_bill_amount)}}
                                 @else
                                 {{utrans("general.notAvailable")}}
                                 @endif
                                 </span>
                              </td>
                              <td>{{ Formatter::date($next_bill_date, false) }}</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>

         {!! Form::open(['action' => '\App\Http\Controllers\BillingController@storeTokenizedCard','id' => 'createStripePaymentMethodForm']) !!}

         <div class="row">
            <div class="col-lg-12 col-12">
               <div class="form-group">
                  <label for="name">{{utrans("billing.nameOnCard")}}</label>
                  {!! Form::text("name",null,['id' => 'name', 'class' => 'form-control', 'placeholder' => utrans("billing.nameOnCard")]) !!}
               </div>
            </div>
         </div>

         <!-- Stripe Elements  -->
         <div class="row">
            <div class="col-lg-12 col-12">
               <div class="form-group" id="stripe_container" data-secret="{{ $secret }}" data-key="{{ $key }}">
                  <label for="name">Card</label>
                  <div id="card-element"></div>
                  <label id="stripe_errors" class="help-block error-help-block"></label>
               </div>
            </div>
         </div>

         <div class="row">
            <div class="col-lg-12 col-12">
               <div class="form-group">
                  <label for="line1">{{utrans("billing.line1")}}</label>
                  {!! Form::text("line1",null,['id' => 'line1', 'class' => 'form-control', 'placeholder' => utrans("billing.line1--placeholder")]) !!}
               </div>
            </div>
            <div class="col-12 col-lg-12">
               <div class="form-group">
                  <label for="country">{{utrans("billing.country")}}</label>
                  {!! Form::select("country",countries(),config("customer_portal.country"),['id' => 'country', 'class' => 'form-control']) !!}
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-12 col-lg-4">
               <div class="form-group">
                  <label for="city">{{utrans("billing.city")}}</label>
                  {!! Form::text("city",null,['id' => 'city', 'class' => 'form-control', 'placeholder' => utrans("billing.city--placeholder")]) !!}
               </div>
            </div>
            <div class="col-12 col-lg-4">
               <div class="form-group">
                  <div id="stateWrapper" @if(count(subdivisions(config("customer_portal.country")))===0) class="csp_style1" @endif">
                     <label for="state">{{utrans("billing.state")}}</label>
                     {!! Form::select("state",subdivisions(config("customer_portal.country")),config("customer_portal.state"),['id' => 'state', 'class' => 'form-control']) !!}
                  </div>
               </div>
            </div>
            <div class="col-12 col-lg-4">
               <div class="form-group">
                  <label for="zip">{{utrans("billing.zip")}}</label>
                  {!! Form::text("zip",null,['id' => 'zip', 'class' => 'form-control', 'placeholder' => utrans("billing.zip--placeholder")]) !!}
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-auto">
               <div class="form-group">
                  <div class="custom-control custom-checkbox-toggle mt-1">
                     {!! Form::checkbox("auto",1,false,['id' => 'auto', 'class' => 'custom-control-input']) !!}
                     <label class="custom-control-label" for="auto"></label>
                  </div>
               </div>
            </div>
            <div class="col mt-1">
               {!! utrans("billing.saveAsAutoPayMethod") !!}
               {{utrans("billing.legalDisclaimer", ["business_name" => config("customer_portal.company_name")])}}
            </div>
         </div>
      </div>
   </div>
   <div class="row mt-5">
      <div class="col-12 col-md-12">
         <input type="hidden" name="payment_tracker_id" value="{{uniqid("", true)}}" />
         <button type="submit" id="add_new_card" class="btn btn-primary">{{utrans("billing.addNewCard")}}</button>
         {!! Form::close() !!}
      </div>
   </div>
</div>
</div>
</div>
@endsection
@section('additionalJS')
<script src="/assets/libs/jquery-payment-plugin/jquery.payment.min.js"></script>
<script src="/assets/js/pages/billing/payment/page_stripe.js"></script>
<script type="text/javascript" src="/assets/libs/js-validation/jsvalidation.min.js"></script>
{!! JsValidator::formRequest('App\Http\Requests\CreateTokenizedCreditCardRequest','#createStripePaymentMethodForm') !!}
@endsection