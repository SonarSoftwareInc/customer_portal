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
         {!! Form::open(['action' => 'BillingController@submitTokenizedPayment', 'id' => 'paymentForm', 'class' => 'mb-4', 'autocomplete' => 'on']) !!}
         <div class="row mt-4">
            <div class="col-12 ">
               <!-- First name -->
               <div class="form-group">
                  <!-- Label -->
                  <label>
                  {{utrans("billing.paymentMethod")}}
                  </label>
                  <!-- Input -->
                  {!! Form::select("payment_method",$paymentMethods,'new_card',['id' => 'payment_method', 'class' => 'form-control']) !!}
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
               <div class="form-group" id="stripe_container" data-secret="{{ $secret }}" data-key="{{ $key }}" >
                  <label for="name">Card</label>
                  <div id="card-element"></div>
                  <label id="stripe_errors" class="help-block error-help-block"></label>
               </div>
            </div>


            <div class="col-12 ">
               <!-- First name -->
               <div class="form-group new_card">
                  <!-- Label -->
                  <label>
                  {{utrans("billing.country")}}
                  </label>
                  <!-- Input -->
                  {!! Form::select("country",countries(),\Illuminate\Support\Facades\Config::get("customer_portal.country"),['id' => 'country', 'class' => 'form-control', 'required' => true]) !!}
               </div>
            </div>
            <div id="stateWrapper" class="col-12 ">
               <!-- First name -->
               <div class="form-group new_card">
                  <!-- Label -->
                  <label>
                  {{utrans("billing.state")}}
                  </label>
                  <!-- Input -->
                  {!! Form::select("state",subdivisions(\Illuminate\Support\Facades\Config::get("customer_portal.country")),\Illuminate\Support\Facades\Config::get("customer_portal.state"),['id' => 'state', 'class' => 'form-control', 'required' => true]) !!}
               </div>
            </div>
            <div class="col-12 ">
               <!-- First name -->
               <div class="form-group new_card">
                  <!-- Label -->
                  <label>
                  {{utrans("billing.line1")}}
                  </label>
                  <!-- Input -->
                  {!! Form::text("line1",null,['id' => 'line1', 'class' => 'form-control', 'placeholder' => utrans("billing.line1"), 'required' => true]) !!}
               </div>
            </div>
            <div class="col-12 ">
               <!-- First name -->
               <div class="form-group new_card">
                  <!-- Label -->
                  <label>
                  {{utrans("billing.city")}}
                  </label>
                  <!-- Input -->
                  {!! Form::text("city",null,['id' => 'city', 'class' => 'form-control', 'placeholder' => utrans("billing.city"), 'required' => true]) !!}
               </div>
            </div>
            <div class="col-12 ">
               <!-- First name -->
               <div class="form-group">
                  <!-- Label -->
                  <label>
                  {{utrans("billing.amountToPay")}}
                  </label>
                  <!-- Input -->
                  {!! Form::number("amount",number_format($billingDetails->balance_due,2,".",""),['id' => 'amount', 'class' => 'form-control', 'placeholder' => utrans("billing.amountToPay"), 'steps' => 'any', 'required' => true]) !!}
               </div>
            </div>
            <div class="col-auto ">
               <!-- Toggle -->
               <div class="custom-control custom-checkbox-toggle mt-1 new_card">
                  {!! Form::checkbox("makeAuto",1,false,['id' => 'makeAuto', 'class' => 'custom-control-input']) !!}
                  <label class="custom-control-label" for="makeAuto"></label>
               </div>
            </div>
            <div class="col mt-1">
               <!-- Help text -->
               <small class="text-muted new_card">
               {{utrans("billing.saveAsAutoPayMethod")}} {{utrans("billing.legalDisclaimer", ["business_name" => config("customer_portal.company_name")])}}
               </small>
            </div>
            <div class="col-12 col-md-12 mt-5">
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
