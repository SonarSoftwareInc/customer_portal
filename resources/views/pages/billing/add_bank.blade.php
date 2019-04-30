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
                           {{utrans("billing.addNewBankAccount")}}
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
            {!! Form::open(['action' => 'BillingController@storeBank', 'id' => 'createPaymentMethodForm']) !!}
            <div class="row">
               <div class="col-lg-12 col-12">
                  <div class="form-group">
                     <label for="name">{{utrans("billing.nameOnAccount")}}</label>
                     {!! Form::text("name",null,['id' => 'name', 'class' => 'form-control', 'placeholder' => utrans("billing.nameOnAccount")]) !!}
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-lg-12 col-12">
                  <div class="form-group">
                     <label for="account_number">{{utrans("billing.accountNumber")}}</label>
                     {!! Form::tel("account_number",null,['id' => 'account_number', 'autocomplete' => 'account_number', 'class' => 'form-control', 'placeholder' => utrans("billing.accountNumber")]) !!}
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-lg-12 col-12">
                  <div class="form-group">
                     <label for="routing_number">{{utrans("billing.routingNumber")}}</label>
                     {!! Form::tel("routing_number",null,['id' => 'routing_number', 'autocomplete' => 'routing_number', 'class' => 'form-control', 'placeholder' => utrans("billing.routingNumber")]) !!}
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-lg-12 col-12">
                  <div class="form-group">
                     <label for="account_type">{{utrans("billing.accountType")}}</label>
                     {!! Form::select("account_type",['checking' => utrans("billing.checking"), 'savings' => utrans("billing.savings")],'checking',['id' => 'account_type', 'autocomplete' => 'account_type', 'class' => 'form-control']) !!}
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-12 col-md-12">
            <button type="submit" class="btn btn-primary">{{utrans("billing.addNewBankAccount")}}</button>
            {!! Form::close() !!}
         </div>
      </div>
   </div>
@endsection
@section('additionalJS')
   <script src="/assets/libs/jquery-payment-plugin/jquery.payment.min.js"></script>
   <script src="/assets/js/pages/billing/payment/page.js"></script>
   <script type="text/javascript" src="/assets/libs/js-validation/jsvalidation.min.js"></script>
   {!! JsValidator::formRequest('App\Http\Requests\CreateBankAccountRequest','#createPaymentMethodForm') !!}
@endsection
