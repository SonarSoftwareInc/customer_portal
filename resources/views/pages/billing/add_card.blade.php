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
         {!! Form::open(['action' => 'BillingController@storeCard', 'id' => 'createPaymentMethodForm']) !!}
         <div class="row">
            <div class="col-lg-12 col-12">
               <div class="form-group">
                  <label for="name">{{utrans("billing.nameOnCard")}}</label>
                  {!! Form::text("name",null,['id' => 'name', 'class' => 'form-control', 'placeholder' => utrans("billing.nameOnCard")]) !!}
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-lg-6 col-12">
               <div class="form-group">
                  <label for="cc_number">{{utrans("billing.creditCardNumber")}}</label>
                  {!! Form::tel("cc-number",null,['id' => 'cc-number', 'autocomplete' => 'cc-number', 'class' => 'cc-number form-control', 'placeholder' => utrans("billing.creditCardNumber")]) !!}
                  <span class="input-group-addon"><i class="fa fa-cc" id="ccIcon" style="width: 25px;"></i></span>
               </div>
            </div>
            <div class="col-lg-3 col-12">
               <div class="form-group">
                  <label for="name">{{utrans("billing.expirationDate")}}</label>
                  {!! Form::tel("expirationDate",null,['id' => 'expirationDate', 'class' => 'form-control', 'placeholder' => utrans("billing.expirationDate")]) !!}
               </div>
            </div>
            <div class="col-lg-3 col-12">
               <div class="form-group">
                  <label for="cvc">{{utrans("billing.cvc")}}</label>
                  <div class="input-group">
                     {!! Form::tel("cvc",null,['id' => 'cvc', 'autocomplete' => 'cvc', 'class' => 'form-control', 'placeholder' => utrans("billing.cvc")]) !!}
                     <span class="input-group-addon"><i class="fa fa-cc" id="ccIcon" style="width: 25px;"></i></span>
                  </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-lg-12 col-12">
               <div class="form-group">
                  <label for="line1">{{utrans("billing.line1")}}</label>
                  {!! Form::text("line1",null,['id' => 'line1', 'class' => 'form-control', 'placeholder' => utrans("billing.line1")]) !!}
               </div>
            </div>
            <div class="col-12 col-lg-12">
               <div class="form-group">
                  <label for="country">{{utrans("billing.country")}}</label>
                  {!! Form::select("country",countries(),\Illuminate\Support\Facades\Config::get("customer_portal.country"),['id' => 'country', 'class' => 'form-control']) !!}
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-12 col-lg-4">
               <div class="form-group">
                  <label for="city">{{utrans("billing.city")}}</label>
                  {!! Form::text("city",null,['id' => 'city', 'class' => 'form-control', 'placeholder' => utrans("billing.city")]) !!}
               </div>
            </div>
            <div class="col-12 col-lg-4">
               <div class="form-group">
                  <div id="stateWrapper" @if(count(subdivisions(\Illuminate\Support\Facades\Config::get("customer_portal.country"))) === 0) style="display:none;" @endif">
                  <label for="state">{{utrans("billing.state")}}</label>
                  {!! Form::select("state",subdivisions(\Illuminate\Support\Facades\Config::get("customer_portal.country")),\Illuminate\Support\Facades\Config::get("customer_portal.state"),['id' => 'state', 'class' => 'form-control']) !!}
               </div>
            </div>
         </div>
         <div class="col-12 col-lg-4">
            <div class="form-group">
               <label for="zip">{{utrans("billing.zip")}}</label>
               {!! Form::text("zip",null,['id' => 'zip', 'class' => 'form-control', 'placeholder' => utrans("billing.zip")]) !!}
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-auto">
            <!-- Toggle -->
            <div class="form-group">
               <div class="custom-control custom-checkbox-toggle mt-1">
                  {!! Form::checkbox("auto",1,false,['id' => 'auto', 'class' => 'custom-control-input']) !!}
                  <label class="custom-control-label" for="auto"></label>
               </div>
            </div>
         </div>
         <div class="col mt-1">
            <small class="text-muted">
            {{utrans("billing.saveAsAutoPayMethod")}}
            </small>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-12 col-md-12">
      <button type="submit" class="btn btn-primary">{{utrans("billing.addNewCard")}}</button>
      {!! Form::close() !!}
   </div>
</div>
</div>
</div>
</div>
@endsection
@section('additionalJS')
<script src="/assets/libs/jquery-payment-plugin/jquery.payment.min.js"></script>
<script src="/assets/js/pages/billing/payment/page.js"></script>
<script type="text/javascript" src="/assets/libs/js-validation/jsvalidation.min.js"></script>
{!! JsValidator::formRequest('App\Http\Requests\CreateCreditCardRequest','#createPaymentMethodForm') !!}
@endsection
