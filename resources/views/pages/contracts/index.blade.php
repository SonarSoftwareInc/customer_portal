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
               {{utrans("headers.contracts")}}
               </h1>
            </div>
            <div class="col-auto">
            </div>
         </div>
         <!-- / .row -->
      </div>
   </div>
   <div class="card mt-4">
      <div class="card-header">
         <h4 class="card-title text-muted mt-3">
            {{utrans("headers.contracts")}}
         </h4>
      </div>
      <div class="table-responsive">
         <table class="table card-table">
            <thead>
               <tr>
                  <th>{{utrans("contracts.name")}}</th>
                  <th>{{utrans("contracts.status")}}</th>
                  <th>{{utrans("contracts.action")}}</th>
               </tr>
            </thead>
            <tbody>
               @if(count($contracts) == 0)
               <TR>
                  <TD colspan="3">{{utrans("contracts.noContracts")}}</TD>
               </TR>
               @endif
               @foreach($contracts as $contract)
               <tr @if($contract->getAcceptanceDatetime() == null) class="warning" @else class="success" @endif>
               <TD>{{$contract->getContractName()}}</TD>
               <TD>@if($contract->getAcceptanceDatetime() == null) {{utrans("contracts.pendingSignature")}} @else {{utrans("contracts.signed")}} @endif</TD>
               <TD>@if($contract->getAcceptanceDatetime() == null) <a href="{{$contract->generateSignatureLink()}}" target="_blank"><button class="btn btn-primary btn-sm"><i class="fe fe-pencil mr-2"></i>{{utrans("contracts.sign")}}</button></a> @else <a href="{{action("ContractController@downloadContractPdf",['id' => $contract->getId()])}}"><button class="btn btn-sm btn-light"><i class="fe fe-file mr-2"></i>{{utrans("contracts.download")}}</button></a> @endif</TD>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
   </div>
</div>
@endsection