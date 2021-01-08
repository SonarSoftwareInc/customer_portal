@extends('layouts.full')
@section('content')
<!-- HEADER -->
<div class="header index-bg pb-5">
   <div class="container-fluid">
      <!-- Body -->
      <div class="header-body-nb">
         <div class="row align-items-end">
            <div class="col">
               <!-- Pretitle -->
               <h6 class="header-pretitle text-secondary-light">
                {{utrans("headers.summary")}}
               </h6>
               <!-- Title -->
               <h1 class="header-title text-white">
               {{utrans("headers.dashboard")}}
               </h1>
            </div>
            <div class="col-auto">
               <!-- Nav -->
               <ul class="nav nav-tabs header-tabs">
                  <li class="nav-item">
                     <a class="nav-link text-right">
                        <h6 class="header-pretitle text-secondary-light">
                         {{utrans("headers.amountDue")}}
                        </h6>
                        <h3 class="text-white mb-0">
                           {{Formatter::currency($values['amount_due'])}}
                        </h3>
                     </a>
                  </li>
               </ul>
            </div>
         </div>
         <!-- / .row -->
      </div>
      <!-- / .header-body -->
      <!-- Footer -->
      <div class="header-footer">
      </div>
   </div>
</div>
<!-- / .header -->
<div class="container-fluid mt--6">
   <div class="row">
      <div class="col-12 col-xl-4">
         @if($values['amount_due'] > 0)
         <div class="card">
            <div class="card-body text-center">
               <div class="row justify-content-center">
                  <div class="col-12 col-xl-10">
                     <!-- Image -->
                     <span class="badge badge-soft-danger">
                     <i class="fe fe-alert-triangle" style="font-size: 20px !important;"></i>
                     </span>
                     <!-- Title -->
                     <h2 class="mb-2 mt-3">
                        {{utrans("headers.amountDue")}}
                     </h2>
                     <!-- Content -->
                     <p class="text-muted">
                        {{Formatter::currency($values['amount_due'])}}
                     </p>
                     <!-- Button -->
                     <a href="{{action("BillingController@makePayment")}}" class="btn btn-white">
                     {{utrans("billing.makePayment")}}
                     </a>
                  </div>
               </div>
               <!-- / .row -->
            </div>
         </div>
         @else
         <div class="card">
            <div class="card-body text-center mb-4 mt-5">
               <div class="row justify-content-center">
                  <div class="col-12 col-xl-10">
                     <!-- Image -->
                     <span class="badge badge-soft-success">
                     <i class="fe fe-thumbs-up" style="font-size: 20px !important;"></i>
                     </span>
                     <!-- Title -->
                     <h2 class="mb-4 mt-4">
                      {{utrans("headers.allPaid")}}
                     </h2>
                  </div>
               </div>
               <!-- / .row -->
            </div>
         </div>
         @endif
      </div>
      <div class="col-12 col-xl-8">
         <div class="row">
            <div class="col-12 col-xl-6">
               <div class="card">
                  <div class="card-body">
                     <div class="row align-items-center">
                        <div class="col">
                           <h6 class="card-title text-uppercase text-muted mb-2">
                              {{utrans("billing.totalBalance")}}
                           </h6>
                           <span class="h2 mb-0">
                           {{Formatter::currency($values['balance_minus_funds'])}}
                           </span>
                        </div>
                        <div class="col-auto">
                           <!-- Icon -->
                           <span class="h2 fe fe-dollar-sign text-muted mb-0"></span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-12 col-xl-6">
               <div class="card">
                  <div class="card-body">
                     <div class="row align-items-center">
                        <div class="col">
                           <h6 class="card-title text-uppercase text-muted mb-2">
                              {{utrans("billing.nextBillDate")}}
                           </h6>
                           <span class="h2 mb-0">@if($values['next_bill_date'] !== null) {{Formatter::date($values['next_bill_date'],false)}} @else {{utrans("general.notAvailable")}} @endif</span>
                        </div>
                        <div class="col-auto">
                           <!-- Icon -->
                           <span class="h2 fe fe-calendar text-muted mb-0"></span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div></div>
            <div class="col-12 col-xl-12">
               <div class="row">
                  <div class="col-12 col-xl-6">
                     <div class="card">
                        <div class="card-body">
                           <div class="row align-items-center">
                              <div class="col">
                                 <h6 class="card-title text-uppercase text-muted mb-2">
                                    {{utrans("billing.nextBillAmount")}}
                                 </h6>
                                 <span class="h2 mb-0">
                                 @if($values['next_bill_amount'] !== null)
                                 {{Formatter::currency($values['next_bill_amount'])}}
                                 @else
                                 {{utrans("general.notAvailable")}}
                                 @endif
                                 </span>
                              </div>
                              <div class="col-auto">
                                 <!-- Icon -->
                                 <span class="h2 fe fe-dollar-sign text-muted mb-0"></span>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  @if($systemSetting->data_usage_enabled === true && $values["currentUsage"] && isset($values["currentUsage"]["billable"]))
                  <div class="col-12 col-xl-6">
                     <div class="card">
                        <div class="card-body">
                           <div class="row align-items-center">
                              <div class="col">
                                 <!-- Title -->
                                 <h6 class="card-title text-uppercase text-muted mb-2">
                                  {{utrans("headers.currentDataUsage")}}
                                 </h6>
                                 <div class="row align-items-center no-gutters">
                                    <div class="col-auto">
                                       <!-- Heading -->
                                       <span class="h2 mr-2 mb-0">
                                       {{$values["currentUsage"]["billable"]}}GB
                                       </span>
                                    </div>
                                    <div class="col">
                                       <!-- Progress -->
                                       <div class="progress progress-sm">
                                          <div class="progress-bar" role="progressbar" style="width: {{$values["currentUsage"]["billable"]}}%" aria-valuenow="{{$values["currentUsage"]["billable"]}}" aria-valuemin="0" aria-valuemax="100"></div>
                                       </div>
                                    </div>
                                 </div>
                                 <!-- / .row -->
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
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="row">
      @if(Config::get("customer_portal.show_detailed_transactions") == true)
      <div class="col-12 col-md-12 col-xl-6">
         <div class="card">
            <div class="card-header">
               <h4 class="card-header-title text-muted">
                  <i class="fe fe-watch mr-3"></i> {{utrans("headers.recentTransactions")}}
               </h4>
            </div>
            <div class="table-responsive">
               <table class="table table-sm card-table">
                  <thead>
                     <tr>
                        <th>{{utrans("billing.transactionType")}}</th>
                        <th>{{utrans("general.date")}}</th>
                        <th>{{utrans("general.amount")}}</th>
                     </tr>
                  </thead>
                  @if(count($transactions) == 0)
                  <tbody>
                     <tr>
                        <td colspan="3">
                           {{utrans("billing.noTransactionsFound")}}
                        </td>
                     </tr>
                  </tbody>
                  @else
                  <tbody>
                     @foreach($transactions as $transaction)
                     <tr>
                        <td>
                           @if(in_array($transaction['type'],['debit','discount'])) 
                           <span class="badge-lg pl-2">{{$transaction['description']}}</span>
                           @else
                           <span class="@if($transaction['type'] != "debit") badge-lg rounded p-2 badge-soft-success @else badge-lg @endif">
                           {{utrans("transaction_types." . $transaction['type'])}}
                           </span>     
                           @endif
                        </td>
                        <td>{{Formatter::date($transaction['date'],false)}}</td>
                        <td>@if($transaction['type'] != "debit")-@endif{{Formatter::currency($transaction['amount'])}}</td>
                     </tr>
                     @if(in_array($transaction['type'],['credit','debit']))
                     @foreach($transaction['taxes'] as $tax)
                     <tr class="active">
                        <td class="push_right"><small>{{utrans("transaction_types.tax",['type' => $tax->description])}}</small></td>
                        <td><small>{{Formatter::date($transaction['date'],false)}}</small></td>
                        <td><small>{{Formatter::currency($tax->amount)}}</small></td>
                     </tr>
                     @endforeach
                     @endif
                     @endforeach
                     @endif
               </table>
            </div>
         </div>
      </div>
      @endif
      <div class="col-12 col-md-12 col-xl-6">
         @if(config("customer_portal.enable_credit_card_payments") == 1)
         <div class="card">
            <div class="card-header">
               <h4 class="card-header-title text-muted">
                  <i class="fe fe-credit-card mr-3"></i> {{utrans("headers.creditCards")}}
               </h4>
               <p class="text-right mt-3">
                  <a class="btn btn-secondary btn-sm" href="{{action("BillingController@createPaymentMethod",['type' => 'credit_card'])}}" role="button">
                  <i class="fe fe-plus"></i>
                  {{utrans("billing.addNewCard")}}
                  </a>
               </p>
            </div>
            <div class="table-responsive">
               <table class="table table-sm card-table">
                  <thead>
                     <tr>
                        <th>{{utrans("billing.last4")}}</th>
                        <th>{{utrans("billing.expiration")}}</th>
                        <th></th>
                     </tr>
                  </thead>
                  <tbody>
                     @if(count($paymentMethods) === 0)
                     <TR>
                        <TD colspan="3">{{utrans("billing.noCreditCardsOnFile")}}</TD>
                     </TR>
                     @else
                     @foreach($paymentMethods as $paymentMethod)
                     @if($paymentMethod->type == "credit card")
                     <TR>
                        <TD><span class="blurry-text">123</span>{{$paymentMethod->identifier}}
                           @if($paymentMethod->auto == 1)
                           <span class="badge badge-soft-success ml-3"><i class="fe fe-check-circle text-success mr-1"></i> {{utrans("headers.autopay")}} </span>
                           @endif
                        </TD>
                        <TD>{{$paymentMethod->expiration_month}}/{{$paymentMethod->expiration_year}}</TD>
                        <TD class="text-right">
                           <div class="dropdown position-static">
                              <a href="#" class="dropdown-ellipses dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-boundary="window">
                              <i class="fe fe-more-vertical"></i>
                              </a>
                              <div class="dropdown-menu dropdown-menu-right" x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(595px, 123px, 0px);" x-out-of-boundaries="">
                                 @if($paymentMethod->auto == 1)
                                 {!! Form::open(['action' => ["BillingController@toggleAutoPay",$paymentMethod->id],'id' => 'deletePaymentMethodForm', 'method' => 'patch']) !!}
                                 <button class="dropdown-item btn btn-sm btn-danger" onClick="submit(); this.disabled=true;this.innerHTML='<i class=&quot;fe fe-loader mt-2 mr-2 &quot;></i> {{utrans("billing.disabling")}}'">
                                 <i class="fe fe-minus-circle mr-2"></i> {{utrans("billing.disableAuto")}}
                                 </button>
                                 {!! Form::close() !!}
                                 @else
                                 {!! Form::open(['action' => ["BillingController@toggleAutoPay",$paymentMethod->id],'id' => 'deletePaymentMethodForm', 'method' => 'patch']) !!}
                                 <button class="dropdown-item btn btn-sm btn-primary" onClick="submit(); this.disabled=true;this.innerHTML='<i class=&quot;fe fe-loader mt-2 mr-2&quot;></i> {{utrans("billing.enabling")}}'">
                                 <i class="fe fe-check-circle mr-2"></i> {{utrans("billing.enableAuto")}}
                                 </button>
                                 {!! Form::close() !!}
                                 @endif
                                 {!! Form::open(['action' => ["BillingController@deletePaymentMethod",$paymentMethod->id],'id' => 'deletePaymentMethodForm', 'method' => 'delete']) !!}
                                 <button class="dropdown-item btn btn-sm btn-danger" onClick="submit(); this.disabled=true;this.innerHTML='<i class=&quot;fe fe-loader mt-2 mr-2 &quot;></i> {{utrans("billing.deleting")}}'">
                                 <i class="fe fe-x-circle mr-2"></i>
                                 {{utrans("actions.delete")}}
                                 </button>
                                 {!! Form::close() !!}
                              </div>
                        </TD>
                        </div>
                     </TR>
                     @endif
                     @endforeach
                     @endif
                  </tbody>
               </table>
            </div>
         </div>
         @endif
         <div class="row">
            @if(config("customer_portal.enable_bank_payments") == 1 || config("customer_portal.enable_gocardless") == 1)
            <div class="col-12 col-md-12 col-xl-12">
               <div class="card">
                     <div class="card-header">
                        <h4 class="card-header-title text-muted"><i class="fe fe-dollar-sign mr-3"></i> {{utrans("headers.bankAccounts")}}</h4>
                        <p class="text-right mt-3">
                           <a class="btn btn-secondary btn-sm" href="{{action("BillingController@createPaymentMethod",['type' => 'bank'])}}" role="button">
                              <i class="fe fe-plus"></i> {{utrans("billing.addNewBankAccount")}}</a>
                        </p>
                     </div>
                  <div class="table-responsive">
                     <table class="table table-sm card-table">
                        <thead>
                           <tr>
                              <th>{{utrans("billing.accountNumber")}}</th>
                              <th></th>
                           </tr>
                        </thead>
                        <tbody>
                           @if(count($paymentMethods) === 0)
                              <TR>
                                 <TD colspan="2">{{utrans("billing.noBankAccounts")}}</TD>
                              </TR>
                           @else
                           @foreach($paymentMethods as $paymentMethod)
                           @if ($paymentMethod->type == "echeck" || $paymentMethod->type == "ach")
                           <TR>
                              <TD>
                                 <span class="blurry-text">000</span>{{$paymentMethod->identifier}}@if($paymentMethod->auto == 1)<span class="badge badge-soft-success ml-3"><i class="fe fe-check-circle text-success mr-1"></i>{{utrans("headers.autopay")}}</span>@endif
                              </TD>
                              <TD class="text-right">
                                 <div class="dropdown position-static">
                                    <a href="#" class="dropdown-ellipses dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-boundary="window">
                                    <i class="fe fe-more-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(595px, 123px, 0px);" x-out-of-boundaries="">
                                       @if($paymentMethod->auto == 1)
                                       {!! Form::open(['action' => ["BillingController@toggleAutoPay",$paymentMethod->id],'id' => 'deletePaymentMethodForm', 'method' => 'patch']) !!}
                                       <button class="dropdown-item btn btn-sm btn-danger" onClick="submit(); this.disabled=true;this.innerHTML='<i class=&quot;fe fe-loader mt-2 mr-2 &quot;></i> {{utrans("billing.disabling")}}'">
                                       <i class="fe fe-minus-circle mr-2"></i>
                                       {{utrans("billing.disableAuto")}}
                                       </button>
                                       {!! Form::close() !!}
                                       @else
                                       {!! Form::open(['action' => ["BillingController@toggleAutoPay",$paymentMethod->id],'id' => 'deletePaymentMethodForm', 'method' => 'patch']) !!}
                                       <button class="dropdown-item btn btn-primary btn-sm" onClick="submit(); this.disabled=true;this.innerHTML='<i class=&quot;fe fe-loader mt-2 mr-2 &quot;></i> {{utrans("billing.enabling")}}'">
                                       <i class="fe fe-check-circle mr-2"></i>
                                       {{utrans("billing.enableAuto")}}
                                       </button>
                                       {!! Form::close() !!}
                                       @endif
                                       {!! Form::open(['action' => ["BillingController@deletePaymentMethod",$paymentMethod->id],'id' => 'deletePaymentMethodForm', 'method' => 'delete']) !!}
                                       <button class="dropdown-item btn btn-sm btn-danger" onClick="submit(); this.disabled=true;this.innerHTML='<i class=&quot;fe fe-loader mt-2 mr-2 &quot;></i> {{utrans("billing.deleting")}}'">
                                       <i class="fe fe-x-circle mr-2"></i>
                                       {{utrans("actions.delete")}}
                                       </button>
                                       {!! Form::close() !!}
                              </TD>
                           </TR>
                           @endif
                           @endforeach
                           @endif
                        </tbody>
                     </table>
                     </div>
                     </div>
                  </div>
                  @endif
               </div>
            </div>
            <div class="col-12 col-md-12 col-xl-6">
               <div class="card">
                  <div class="card-header">
                     <h4 class="card-header-title text-muted">
                        <i class="fe fe-inbox mr-3"></i>{{utrans("headers.invoices")}}
                     </h4>
                  </div>
                  <div class="table-responsive">
                     <table class="table table-sm card-table">
                        <thead>
                           <tr>
                              <th>{{utrans("general.date")}}</th>
                              <th>{{utrans("billing.invoiceNumber")}}</th>
                              <th>{{utrans("billing.remainingDue")}}</th>
                              <th>{{utrans("billing.dueDate")}}</th>
                              <th>{{utrans("billing.viewInvoice")}}</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if(count($invoices) == 0)
                           <TR>
                              <TD colspan="4">{{utrans("billing.noInvoicesFound")}}</TD>
                           </TR>
                           @else
                           @foreach($invoices as $invoice)
                           <TR>
                              <TD>{{Formatter::date($invoice->date,false)}}</TD>
                              <TD>{{$invoice->id}}</TD>
                              <TD>{{Formatter::currency(bcadd($invoice->remaining_due, $invoice->child_remaining_due,2))}}</TD>
                              <TD>{{Formatter::date($invoice->due_date,false)}}</TD>
                              <TD>
                                 <a class="btn btn-sm" href="{{action("BillingController@getInvoicePdf",['invoices' => $invoice->id])}}" role="button">
                                 <i class="fe fe-file-text mr-1"></i>
                                 {{utrans("billing.downloadInvoice")}}
                                 </a>
                              </TD>
                           </TR>
                           @endforeach
                           @endif
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection