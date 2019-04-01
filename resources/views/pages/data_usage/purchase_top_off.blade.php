@extends('layouts.full')
@section('content')
<div class="main-content">
<div class="container-fluid">
   <div class="row justify-content-center">
      <div class="col-12">
         <div class="header mt-md-5">
            <div class="header-body">
               <div class="row align-items-center">
                  <div class="col">
                     <h6 class="header-pretitle">
                     </h6>
                     <h1 class="header-title">
                        {{utrans("headers.purchaseAdditionalData")}}
                     </h1>
                  </div>
                  <div class="col-auto">
                  </div>
               </div>
            </div>
         </div>
         {!! Form::open(['action' => 'DataUsageController@addTopOff', 'id' => 'topOffForm', 'autocomplete' => 'on']) !!}
	<h6 class="header-pretitle mb-4">
            <i class="fe fe-zap"></i> {{utrans("data_usage.quantity")}}
        </h6>
         <div class="card">
            <div class="card-header">
               <div class="row align-items-center">
                  <div class="col">
                     <h4 class="card-header-title">
                        <input type="range" min="1" max="20" step="1" value="1" id="quantity" class="custom-range" name="quantity" onchange="updateCalcValue(this.value);">
                     </h4>
                  </div>
                  <div class="col-auto">
                     <a href="#" class="btn btn-sm btn-white" onclick="addValue()">
                     +
                     </a>
                  </div>
               </div>
            </div>
            <div class="card-header">
               <div class="row align-items-center">
                  <div class="col">
                     <div class="card-body">
                        <span id="calculatedAmount">{{utrans("data_usage.topOffTotal",['count' => $policyDetails->overage_units_in_gigabytes, 'cost' => \App\Facades\Formatter::currency($policyDetails->overage_cost)])}}</span> <i class="fe fe-check-circle text-success"></i>
                     </div>
                  </div>
                  <div class="col-auto">
                     <button type"submit" class="btn btn-primary mb-3">
                     {{utrans("data_usage.confirmTopOffAddition")}}
                     </button>
                  </div>
                  <input type="hidden" id="cost" value="{{$policyDetails->overage_cost}}">
                  <input type="hidden" id="units" value="{{$policyDetails->overage_units_in_gigabytes}}">
                  {!! Form::close() !!}
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section('additionalJS')
<script>
   var units = $("#units").val();
   var cost = $("#cost").val();
   
   function addValue() {
       valX = document.getElementById('quantity').value;
       valX = parseInt(valX, 10);
       valY = valX + 1;
       updateCalcValue(valY);
       document.getElementById('quantity').value = valY;
   }
   
   function updateCalcValue(value) {
       $("#calculatedAmount").html(Lang.get("data_usage.topOffTotal", {
           count: value * units,
           cost: (cost * value).formatCurrency(_portal.currencySymbol)
       }));
   }
</script>
@endsection