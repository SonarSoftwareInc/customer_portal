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
                {{utrans("headers.summary")}}
               </h6>
               <!-- Title -->
               <h1 class="header-title">
                {{utrans("headers.dataUsage")}}
               </h1>
            </div>
            @if($policyDetails->allow_user_to_purchase_capacity === true)
            <div class="col-auto">
               <a href="{{action("DataUsageController@showTopOff")}}" class="btn btn-primary">
               {{utrans("data_usage.purchaseAdditionalData")}} <i class="fe fe-zap"></i>
               </a>
            </div>
            @endif
         </div>
         <!-- / .row -->
      </div>
   </div>
</div>
@if($policyDetails->has_policy === true)
<div class="col-12 col-sm-12 col-md-12">
   <!-- Card -->
   <div class="card">
      <div class="card-header">
         <h4 class="card-header-title">
            {{utrans("headers.currentUsage")}}
         </h4>
      </div>
      <div class="card-body">
         <div class="row align-items-center">
            <div class="col">
               <div class="row align-items-center no-gutters">
                  <div class="col-auto">
                     <!-- Heading -->
                     <span class="h2 mr-4 mb-0">
                     {{ $currentUsage["billable"] }}GB
                     </span>
                  </div>
                  <div class="col">
                     <!-- Progress -->
                     <div class="progress progress-sm">
                        <div class="progress-bar" role="progressbar" style="width: {{$usagePercentage}}%" aria-valuenow="{{$usagePercentage}}" aria-valuemin="0" aria-valuemax="100"></div>
                     </div>
                  </div>
               </div>
               <!-- / .row -->
            </div>
            <div class="col-auto">
               <!-- Heading -->
               <span class="h2 ml-1 mr-2 mb-0">
               {{ $calculatedCap }}GB
               </span>
            </div>
            <div class="col-auto">
               <!-- Icon -->
               <span class="h2 fe fe-activity text-muted mb-0"></span>
            </div>
         </div>
         <!-- / .row -->
      </div>
   </div>
</div>
@endif                     
<div class="col-12 col-sm-12 col-md-12">
   <!-- Card -->
   <div class="card">
      <div class="card-header">
         <h4 class="card-title text-muted mt-3">
           {{utrans("data_usage.usageHistory")}}
         </h4>
      </div>
      <div class="card-body">
         <div class="row align-items-center">
            <div class="col">
               <div class="panel-body">
                  <canvas id="historicalUsage" height="125"></canvas>
               </div>
               <p class="mt-4 text-right comment-time-dark">{{utrans("data_usage.monthlyGraphHeader")}}</p>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section('additionalJS')
<script>
   var historicalUsage = {!! $historicalUsage !!};
   var dataUsageLabel = '{{utrans("data_usage.usage")}}';
</script>
<script src="/assets/js/pages/data_usage/index.js" type="text/javascript"></script>
@endsection