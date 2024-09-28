@extends('layouts.full')
@section('content')
@if (isset($values["currentUsage"]["billable"]))
<style nonce="{{ csp_nonce() }}">
   #usage-progressbar {
     width:  {{$values["currentUsage"]["billable"]}}%
   }
   /* @media screen and (min-width: 768px) {
      .resposive-row {
         margin-top: -10px;
      }
   } */
   ul.pagination {
      padding-left: 10px !important;
      margin-bottom: 10px !important;
   }
</style>
@endif
<style nonce="{{ csp_nonce() }}">
   .left-round-border {
      border-top-left-radius: 0px !important;
      border-bottom-left-radius: 0px !important;
      border-left: white !important;
   }
   .hr-text {
      display: flex;
      align-items: center;
      margin: 1.5rem 0;
      font-size: .825rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.2em;
      line-height: 1rem;
      color: #95aac9;
      height: 1px;
   }
   .hr-text:after, .hr-text:before {
      flex: 1 1 auto;
      height: 1px;
      background-color: #dce1e7;
   }

   .hr-text:before {
      content: "";
      margin-right: .5rem;
   }
   .hr-text:after {
      content: "";
      margin-left: .5rem;
   }
</style>
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
      <div class="col-12 col-xl-3">
         <div class="card">
            <div class="card-body">
                <div class="row">
                   <div class="col-12 col-xl-12">
                       <!-- Title -->
                        <div class="d-flex justify-content-between align-items-center">
                           <h2>Wi-Fi Management</h2>
                           <div class="wifi-qrcode"></div>
                        </div>
                        {{-- <h2 class="pb-4">Wi-Fi Management</h2>
                        <div class="wifi-qrcode"></div> --}}
                        <!-- Form -->
                        {!! Form::open(['action' => '\App\Http\Controllers\BillingController@wifiManagement', 'id' => 'wifiForm', 'method' => 'PATCH']) !!}
                           <div class="mb-3">
                              <label for="wifi" class="form-label">Wi-Fi Band</label>
                              <select name="wifi_band" id="wifi" class="form-control form-select">
                                 @if(!empty($wifiData))
                                       @foreach($wifiData as $wifi)
                                          <option value="{{ $wifi['wifi_band'] }}">Personalize - {{ $wifi['wifi_band'] }}</option>
                                       @endforeach
                                       <option value="both" selected>Both</option>
                                 @else
                                       <option value="">No data found</option>
                                 @endif
                              </select>
                           </div>
                           <div class="mb-3">
                              <label for="ssid" class="form-label">Wi-Fi Name</label>
                              <input type="text" name="ssid" class="form-control" id="ssid" 
                                    value="{{ !empty($wifiData) ? $wifiData[0]['ssid'] : '' }}" {{ empty($wifiData) ? 'disabled' : '' }}>
                           </div>
                           <div class="mb-3">
                              <label for="password" class="form-label">Password</label>
                              <div class="input-group">
                                 <input type="password" name="password" class="form-control" id="password" 
                                    value="{{ !empty($wifiData) ? $wifiData[0]['wifi_password'] : '' }}" {{ empty($wifiData) ? 'disabled' : '' }}>
                                 <span class="input-group-text left-round-border">
                                     <a href="javascript:void(0)"
                                         class="link-secondary fe fe-eye field-icon toggle-password"
                                         toggle="#password">
                                     </a>
                                 </span>
                             </div>
                              {{-- <input type="password" name="password" class="form-control" id="password" 
                                    value="{{ !empty($wifiData) ? $wifiData[0]['wifi_password'] : '' }}" {{ empty($wifiData) ? 'disabled' : '' }}> --}}
                           </div>
                           <div class="text-center">
                              <button type="button" id="reset-button" class="btn btn-danger w-25 mr-2" hidden>Cancel</button>
                              <button type="submit" id="edit-button" class="btn btn-success w-25" hidden>Submit</button>
                           </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                <!-- / .row -->
            </div>
        </div>
      </div>
      <div class="col-12 col-xl-3">
         <div class="row resposive-row">
            <div class="col-12 col-xl-12">
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
            <div class="col-12 col-xl-12">
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
            <div class="col-12 col-xl-12">
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
                                    <div id="usage-progressbar" class="progress-bar" role="progressbar" aria-valuenow="{{$values["currentUsage"]["billable"]}}" aria-valuemin="0" aria-valuemax="100"></div>
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
      @if(!empty($account_service_id))
         <div class="col-12 col-xl-3">
            <div class="card">
               <div class="card-body">
                  <div class="row">
                     <div class="col-12 col-xl-12">
                           <div class="text-center {{ empty($service) ? 'text-secondary' : '' }}">
                                 <h2>{{ empty($service) ? 'No services currently subscribed' : 'Service Information' }}</h2>
                           </div>
                           <div class="d-flex flex-column justify-content-center align-items-center">
                              @if(!empty($service))
                              <div class="text-center">
                                    <h4 class="mb-1">{{ $service[0]['name'] }}</h4>
                                    <h1 class="mb-1 text-secondary">${{ number_format(($service[0]['amount']/100), 2) }}</h1>
                                    <p class="mb-1">Month</p>
                              </div>
                              <div class="hr-text col-lg-12">Bandwidth</div>
                              <div class="text-center"> 
                                    <p class="mb-3">Download speed : {{ number_format($service[0]['download_speed'] / 1000, 2) }}Mbps</p>
                                    <p class="mb-1">Upload speed : {{ number_format($service[0]['upload_speed'] / 1000, 2) }}Mbps</p>
                                    @if($account_status_name == 'Active')
                                       <a href="{{action([\App\Http\Controllers\BillingController::class, 'packageIndex'])}}" class="btn btn-success mt-2" >Upgrade</a>
                                    @else
                                       <a href="javascript:void(0)" class="btn btn-success my-2 disabled" disabled>Upgrade</a><br>
                                       <small class="text-danger">You are not allowed to upgrade</small>
                                    @endif
                              </div>
                              @else
                                 @if($account_status_name == 'Active')
                                    <a href="{{action([\App\Http\Controllers\BillingController::class, 'packageIndex'])}}" class="btn btn-success mt-2" >Upgrade</a>
                                 @else
                                    <a href="javascript:void(0)" class="btn btn-success my-2 disabled" disabled>Upgrade</a><br>
                                    <small class="text-danger">You are not allowed to upgrade</small>
                                 @endif
                              @endif
                           </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      @else
         <div class="col-12 col-xl-3">
            <div class="card">
               <div class="card-body text-center mb-4 mt-5">
                  <div class="row justify-content-center">
                     <div class="col-12 col-xl-10">
                        <span class="badge badge-soft-warning">
                        <i class="fe fe-alert-circle"></i>
                        </span>
                        <h3 class="mb-4 mt-4 text-warning">You are currently unable to subscribe to a new package.</h3>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      @endif
      <div class="col-12 col-xl-3">
         @if($values['amount_due'] > 0)
         <div class="card">
            <div class="card-body text-center">
               <div class="row justify-content-center">
                  <div class="col-12 col-xl-10">
                     <!-- Image -->
                     <span class="badge badge-soft-danger">
                     <i class="fe fe-alert-triangle cspfont1"></i>
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
                     <a href="{{action([\App\Http\Controllers\BillingController::class, 'makePayment'])}}" class="btn btn-white">
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
                     <i class="fe fe-thumbs-up cspfont1"></i>
                     </span>
                     <!-- Title -->
                     <h2 class="mb-4 mt-4">
                      {{utrans("headers.allPaid")}}
                     </h2>
                      <!-- Button -->
                      <a href="{{action([\App\Http\Controllers\BillingController::class, 'makePayment'])}}" class="btn btn-white">
                          {{utrans("billing.makePayment")}}
                      </a>
                  </div>
               </div>
               <!-- / .row -->
            </div>
         </div>
         @endif
      </div>
   </div>
   <div class="row">
      @if(config("customer_portal.show_detailed_transactions") == true)
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
                  </tbody>
                  @endif
               </table>
                {{ $transactions->links() }}
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
                  <a class="btn btn-secondary btn-sm" href="{{action([\App\Http\Controllers\BillingController::class, 'createPaymentMethod'],['type' => 'credit_card'])}}" role="button">
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
                        <th colspan="2"></th>
                     </tr>
                  </thead>
                  <tbody>
                     @if(count($paymentMethods) === 0)
                     <TR>
                        <TD colspan="4">{{utrans("billing.noCreditCardsOnFile")}}</TD>
                     </TR>
                     @else
                     @foreach($paymentMethods as $paymentMethod)
                     @if($paymentMethod->type == "credit card")
                     <TR>
                        <TD>{{$paymentMethod->identifier}}
                           @if($paymentMethod->auto == 1)
                           <span class="badge badge-soft-success ml-3"><i class="fe fe-check-circle text-success mr-1"></i> {{utrans("headers.autopay")}} </span>
                           @endif
                        </TD>
                        <TD>{{$paymentMethod->expiration_month}}/{{$paymentMethod->expiration_year}}</TD>
                        <TD class="text-right">
                           @if($paymentMethod->auto == 1)
                           {!! Form::open(['action' => ["\App\Http\Controllers\BillingController@toggleAutoPay",$paymentMethod->id],'id' => 'enablePaymentMethodForm','method' => 'patch']) !!}
                           <button class="btn btn-sm btn-disable-with-msg-on-click" data-message="{{utrans("billing.disabling")}}">
                           <i class="fe fe-minus-circle mr-2"></i> {{utrans("billing.disableAuto")}}
                           </button>
                           {!! Form::close() !!}
                           @else
                           {!! Form::open(['action' => ["\App\Http\Controllers\BillingController@toggleAutoPay",$paymentMethod->id],'id' => 'enablePaymentMethodForm','method' => 'patch']) !!}
                           <button class="btn btn-sm btn-disable-with-msg-on-click" data-message="{{utrans("billing.enabling")}}">
                           <i class="fe fe-check-circle mr-2"></i> {{utrans("billing.enableAuto")}}
                           </button>
                           {!! Form::close() !!}
                           @endif
                        </TD>
                        <TD class="text-right">
                           {!! Form::open(['action' => ["\App\Http\Controllers\BillingController@deletePaymentMethod",$paymentMethod->id],'id' => 'deletePaymentMethodForm','method' => 'delete']) !!}
                           <button class="btn btn-sm btn-disable-with-msg-on-click" data-message="{{utrans("billing.deleting")}}">
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
         @endif
         <div class="row">
            @if(config("customer_portal.enable_bank_payments") == 1 || config("customer_portal.enable_gocardless") == 1)
            <div class="col-12 col-md-12 col-xl-12">
               <div class="card">
                  <div class="card-header">
                     <h4 class="card-header-title text-muted"><i class="fe fe-dollar-sign mr-3"></i> {{utrans("headers.bankAccounts")}}</h4>
                     <p class="text-right mt-3">
                        <a class="btn btn-secondary btn-sm" href="{{action([\App\Http\Controllers\BillingController::class, 'createPaymentMethod'],['type' => 'bank'])}}" role="button">
                           <i class="fe fe-plus"></i> {{utrans("billing.addNewBankAccount")}}</a>
                     </p>
                  </div>
                  <div class="table-responsive">
                     <table class="table table-sm card-table">
                        <thead>
                           <tr>
                              <th>{{utrans("billing.accountNumber")}}</th>
                              <th colspan="2"></th>
                           </tr>
                        </thead>
                        <tbody>
                           @if(count($paymentMethods) === 0)
                              <TR>
                                 <TD colspan="3">{{utrans("billing.noBankAccounts")}}</TD>
                              </TR>
                           @else
                           @foreach($paymentMethods as $paymentMethod)
                           @if ($paymentMethod->type == "echeck" || $paymentMethod->type == "ach")
                           <TR>
                              <TD>
                                  ****{{$paymentMethod->identifier}}@if($paymentMethod->auto == 1)<span class="badge badge-soft-success ml-3"><i class="fe fe-check-circle text-success mr-1"></i>{{utrans("headers.autopay")}}</span>@endif
                              </TD>
                              <TD class="text-right">
                                 @if($paymentMethod->auto == 1)
                                 {!! Form::open(['action' => ["\App\Http\Controllers\BillingController@toggleAutoPay",$paymentMethod->id],'id' => 'deletePaymentMethodForm','method' => 'patch']) !!}
                                 <button class="btn btn-sm" onClick="submit(); this.disabled=true;this.innerHTML='<i class=&quot;fe fe-loader mt-2 mr-2 &quot;></i> {{utrans("billing.disabling")}}'">
                                 <i class="fe fe-minus-circle mr-2"></i>
                                 {{utrans("billing.disableAuto")}}
                                 </button>
                                 {!! Form::close() !!}
                                 @else
                                 {!! Form::open(['action' => ["\App\Http\Controllers\BillingController@toggleAutoPay",$paymentMethod->id],'id' => 'deletePaymentMethodForm','method' => 'patch']) !!}
                                 <button class="btn btn-sm" onClick="submit(); this.disabled=true;this.innerHTML='<i class=&quot;fe fe-loader mt-2 mr-2 &quot;></i> {{utrans("billing.enabling")}}'">
                                 <i class="fe fe-check-circle mr-2"></i>
                                 {{utrans("billing.enableAuto")}}
                                 </button>
                                 {!! Form::close() !!}
                                 @endif
                              </TD>
                              <TD class="text-right">
                                 {!! Form::open(['action' => ["\App\Http\Controllers\BillingController@deletePaymentMethod",$paymentMethod->id],'id' => 'deletePaymentMethodForm','method' => 'delete']) !!}
                                 <button class="btn btn-sm" onClick="submit(); this.disabled=true;this.innerHTML='<i class=&quot;fe fe-loader mt-2 mr-2 &quot;></i> {{utrans("billing.deleting")}}'">
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
                        <TD colspan="5">{{utrans("billing.noInvoicesFound")}}</TD>
                     </TR>
                     @else
                     @foreach($invoices as $invoice)
                     <TR>
                        <TD>{{Formatter::date($invoice->date,false)}}</TD>
                        <TD>{{$invoice->id}}</TD>
                        <TD>{{Formatter::currency(bcadd($invoice->remaining_due, $invoice->child_remaining_due,2))}}</TD>
                        <TD>{{Formatter::date($invoice->due_date,false)}}</TD>
                        <TD>
                           <a class="btn btn-sm" href="{{action([\App\Http\Controllers\BillingController::class, 'getInvoicePdf'],['invoices' => $invoice->id])}}" role="button">
                           <i class="fe fe-file-text mr-1"></i>
                           {{utrans("billing.downloadInvoice")}}
                           </a>
                        </TD>
                     </TR>
                     @endforeach
                     @endif
                  </tbody>
               </table>
                {{ $invoices->links() }}
            </div>
         </div>
      </div>
      <div class="col-12 col-sm-10 col-md-10 col-lg-6 col-xl-4">
         <div class="card">
             <div style="display: {{ $svgDisplay }};">
                 {!! $svg !!}
            </div>
         </div>
      </div>
   </div>
</div>
</div><!-- #main-content -->
@endsection

@section('additionalJS')
<script type="text/javascript" src="{{ asset('assets/js/jquery-qrcode.min.js') }}" nonce="{{ csp_nonce() }}"></script>
<script nonce="{{ csp_nonce() }}">
   document.addEventListener('DOMContentLoaded', function() {
      
      // Interactive Buttons
      const ssidInput = document.getElementById('ssid');
      const passwordInput = document.getElementById('password');
      const submitButton = document.getElementById('edit-button');
      const cancelButton = document.getElementById('reset-button');

      // Initial SSID and PASSWORD
      let initialSSID = ssidInput.value.trim();
      let initialPassword = passwordInput.value.trim();

      function toggleSubmitButton() {
            if (
               ssidInput.value.trim() !== '' &&
               passwordInput.value.trim() !== '' &&
               (ssidInput.value.trim() !== initialSSID || passwordInput.value.trim() !== initialPassword)
            ) {
               submitButton.hidden = false;
               cancelButton.hidden = false;
            } else {
               submitButton.hidden = true;
               cancelButton.hidden = true;
            }
      }

      function resetInputsAndToggleButton() {
            ssidInput.value = initialSSID;
            passwordInput.value = initialPassword;
            submitButton.hidden = true;
            cancelButton.hidden = true;
      }

      ssidInput.addEventListener('input', toggleSubmitButton);
      passwordInput.addEventListener('input', toggleSubmitButton);
      cancelButton.addEventListener('click', resetInputsAndToggleButton);

      // Qr Code Generation
      if (initialSSID) {
         let qr_text = 'WIFI:T:nopass;S:'+initialSSID+';P:'+initialPassword+';H:;;';
         makeQRCode(qr_text, $(".wifi-qrcode"));
      }
      
      function makeQRCode(qr_text, idClass) {
         let qrparams = {
               render: 'image',
               minVersion: 3,
               mode: Number(0),
               fill: "#797e85",
               background: "#ffffff",
               size: 90,
               left: 0,
               top: 0,
               text: qr_text,
               radius: 0.5,
               label: 'QR Code',
               quiet: 3,
         };
         idClass.html("")
         $(document).ready(function () {
               idClass.qrcode(qrparams);
         });
      }

      // password show hide
      $(".toggle-password").click(function () {
         $(this).toggleClass("fe-eye fe-eye-off");
         var input = $($(this).attr("toggle"));
         if (input.attr("type") == "password") {
               input.attr("type", "text");
         } else {
               input.attr("type", "password");
         }
      });

      // Get Wifi Management Data
      const wifiData = @json($wifiData);
      const wifiSelect = document.getElementById('wifi');

      // Get SSID and Password accroding the Wifi Band
      wifiSelect.addEventListener('change', function () {
         const selectedBand = this.value;

         if (selectedBand === 'both') {
            // Set ssidInput and passwordInput values to wifiData[0]'s values
            if (wifiData.length > 0) {
               ssidInput.value = wifiData[0]['ssid'];
               passwordInput.value = wifiData[0]['wifi_password'];
            }
         } else {
            const selectedWifi = wifiData.find(wifi => wifi.wifi_band === selectedBand);

            if (selectedWifi) {
               ssidInput.value = selectedWifi.ssid;
               passwordInput.value = selectedWifi.wifi_password;
            }
         }

         
         initialSSID = ssidInput.value;
         initialPassword = passwordInput.value;

         // Generate QR code
         if (initialSSID) {
            qr_text = 'WIFI:T:nopass;S:' + initialSSID + ';P:' + initialPassword + ';H:;;';
            makeQRCode(qr_text, $(".wifi-qrcode"));
         } 

         // Toggle submit button
         toggleSubmitButton();
      });
   });
</script>
@endsection