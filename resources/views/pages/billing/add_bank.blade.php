@extends('layouts.full')

@section('additionalCSS')
<style>
#canadian-routing-section .form-group {
    border: 1px solid #e3ebf0;
    border-radius: 0.375rem;
    padding: 1rem;
    background-color: #f8f9fa;
}

#canadian-routing-section .form-group label {
    color: #495057;
    font-weight: 500;
}

#canadian-routing-section .small {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

#canadian-input-fields {
    transition: opacity 0.3s ease-in-out;
}

.is-valid {
    border-color: #28a745;
}

.is-invalid {
    border-color: #dc3545;
}
</style>
@endsection

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

            {!! Form::open(['action' => '\App\Http\Controllers\BillingController@storeBank','id' => 'createPaymentMethodForm']) !!}
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="form-group">
                        <label for="name">{{utrans("billing.nameOnAccount")}}</label>
                        {!! Form::text("name",null,['id' => 'name', 'class' => 'form-control', 'placeholder' => utrans("billing.nameOnAccount--placeholder"), 'maxlength' => 255]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="form-group">
                        <label for="account_number">{{utrans("billing.accountNumber")}}</label>
                        {!! Form::tel("account_number",null,['id' => 'account_number', 'autocomplete' => 'account_number', 'class' => 'form-control', 'placeholder' => utrans("billing.accountNumber--placeholder")]) !!}
                    </div>
                </div>
            </div>
            
            <!-- Canadian Routing Number Fields -->
            <div class="row" id="canadian-routing-section" style="display: none;">
                <div class="col-lg-12 col-12">
                    <div class="form-group">
                        <!-- Option to use standard routing for US banks -->
                        <div class="mb-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="use_standard_routing">
                                <label class="custom-control-label" for="use_standard_routing">
                                    <small>{{ utrans("billing.standardRoutingNumberInfo") }}</small>
                                </label>
                            </div>
                        </div>

                        <!-- Canadian input fields (Institution + Transit) -->
                        <div id="canadian-input-fields">
                            <div class="row">
                                <div class="col-4">
                                    <label for="institution_number">{{utrans("billing.institutionNumber")}}</label>
                                    {!! Form::tel("institution_number",null,['id' => 'institution_number', 'class' => 'form-control', 'placeholder' => utrans("billing.institutionNumber--placeholder"), 'maxlength' => '3']) !!}
                                </div>
                                <div class="col-8">
                                    <label for="transit_number">{{utrans("billing.transitNumber")}}</label>
                                    {!! Form::tel("transit_number",null,['id' => 'transit_number', 'class' => 'form-control', 'placeholder' => utrans("billing.transitNumber--placeholder"), 'maxlength' => '5']) !!}
                                </div>
                            </div>
                            <small class="form-text text-muted">{{utrans("billing.routingNumberCanada--placeholder")}}</small>
                        </div>
                        
                        <!-- Hidden field for combined routing number -->
                        {!! Form::hidden("routing_number_canadian",null,['id' => 'routing_number_canadian']) !!}
                    </div>
                </div>
            </div>

            <!-- Standard Routing Number (Non-Canadian) -->
            <div class="row" id="standard-routing-section">
                <div class="col-lg-12 col-12">
                    <div class="form-group">
                        <label for="routing_number">{{utrans("billing.routingNumber")}}</label>
                        {!! Form::tel("routing_number",null,['id' => 'routing_number', 'autocomplete' => 'routing_number', 'class' => 'form-control', 'placeholder' => utrans("billing.routingNumber--placeholder")]) !!}
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
                    <!-- Toggle -->
                    <div class="form-group">
                        <div class="custom-control custom-checkbox-toggle mt-1">
                            {!! Form::checkbox("auto",1,false,['id' => 'auto', 'class' => 'custom-control-input']) !!}
                            <label class="custom-control-label" for="auto"></label>
                        </div>
                    </div>
                </div>
                <div class="col mt-1">
                    {!! utrans("billing.saveAsAutoPayMethodAccount", ["business_name" => config("customer_portal.company_name")]) !!}
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-12 col-md-12">
            <input type="hidden" name="payment_tracker_id" value="{{uniqid("", true)}}" />
            <button type="submit" id="createPaymentMethodSubmitButton" class="btn btn-primary">{{utrans("billing.addNewBankAccount")}}</button>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
@section('additionalJS')
<script src="/assets/libs/jquery-payment-plugin/jquery.payment.min.js"></script>
<script src="/assets/js/pages/billing/payment/page.js"></script>
<script src="/assets/js/pages/billing/canadian_routing.js"></script>
<script type="text/javascript" src="/assets/libs/js-validation/jsvalidation.min.js"></script>
{!! JsValidator::formRequest('App\Http\Requests\CreateBankAccountRequest','#createPaymentMethodForm') !!}
@endsection