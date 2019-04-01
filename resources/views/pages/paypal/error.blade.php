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
            <!-- Title -->
            <h1 class="header-title">
               {{utrans("headers.failure")}}
            </h1>
         </div>
         <div class="col-auto">
         </div>
      </div>
      <!-- / .row -->
   </div>
</div>
<div class="row">
   <div class="col-12">
      <div class="card mt-4">
         <div class="card-body">
            <p>
               {{$error}}
            </p>
            <p>
               <a href="{{action("BillingController@index")}}">{{utrans("billing.backToBillingPage")}}</a>
            </p>
         </div>
      </div>
   </div>
</div>
@endsection