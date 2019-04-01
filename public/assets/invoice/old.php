<div class="page">
    @if($isPastDue == true && $totalRemainingDue > 0 && $showPastDueStamp == 1)
    <div id="pastDue">
        {{trans("invoice.pastDue")}}
    </div>
    @endif
    <div id="fold2BaseTagline">
        <strong>{{trans("invoice.customerService",[],$locale)}}</strong>
        <BR> @if($company->phoneNumbers()->first() !== null) {{trans("invoice.callUsAtNumber",['number' => $company->phoneNumbers()->first()->_formatted()],$locale)}} @endif
        <BR> @if($company->website) {!! trans("invoice.visitUsAt",['website' => $company->website],$locale) !!} @endif
        <div id="lateFeeArea">
            {{$lateFeeString}}
        </div>
    </div>
    <div id="messageArea">
        {!! nl2br($invoice->message) !!}
    </div>
    @if(file_exists(storage_path("logos/companyLogo.png")))
    <div id="companyLogo">
        <img src="/usr/share/sonar/storage/logos/companyLogo.png" style="background-color:white;">
    </div>
    @endif
    <div id="invoiceOverview">
        @if($invoice->accountBillingCompletion === null)
        <h4>{{trans("invoice.invoiceDate")}}</h4> {{Formatter::nonUtcDate($invoice->date)}} @else
        <h4>{{trans("invoice.servicePeriod",[],$locale)}}</h4> {{trans("invoice.serviceBetween",['start_date' => Formatter::nonUtcDate($invoice->date), 'end_date' => Formatter::nonUtcDate(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $invoice->date . " 00:00:00")->addMonths($invoice->number_of_months)->subDay()->toDateString())],$locale)}} @endif
        <h4>{{trans("invoice.dueDate",[],$locale)}}</h4> {{Formatter::nonUtcDate($invoice->due_date)}}
        <h4>{{trans("invoice.amountDue",[],$locale)}}</h4> {{Formatter::currency($totalRemainingDue)}}
        <h4>@if($mode == \Sonar\Generators\InvoiceGenerator::INVOICE){{trans("invoice.invoiceNumber",[],$locale)}}@else{{trans("invoice.statementNumber",[],$locale)}}@endif</h4> {{$invoice->id}} @if($totalUsed != null)
        <h4>{{trans("invoice.dataUsage",[],$locale)}}</h4> {{$totalUsed}} @if($cap != null) / {{$cap}} @endif @endif
    </div>
    <div class="fold">
        <div id="fold1">
            <div id="returnAddress">
                {{ strtoupper($company->company_name) }}
                <BR> {!! strtoupper(Formatter::address($company->remittanceAddress[0])) !!}
            </div>
            <div id="toAddress">
                {{ strtoupper($account->name) }}
                <BR> {!! strtoupper(Formatter::address($address)) !!}
            </div>
        </div>
    </div>
    <div class="fold">
        <div id="fold2">
            @if(SonarSystem::get("Show Account ID On Invoice") == 1)
            <div id="accountInfo">
                <strong>{{trans("invoice.accountNumber",[],$locale)}}</strong> {{$account->id}}
            </div>
            @endif
            <div>
                <table border="0" id="accountSummary">
                    <TR>
                        <TD class='title'>{{utrans("invoice.charges")}}</TD>
                        <TD class="summaryAmount">{{Formatter::currency($totalCharges)}}</TD>
                    </TR>
                    @if($mode == "statement")
                    <TR>
                        <TD class='title'>{{utrans("invoice.priorBalance")}}</TD>
                        <TD class="summaryAmount">{{Formatter::currency($previousBalance)}}</TD>
                    </TR>
                    @endif
                    <TR>
                        <TD class='title'>{{utrans("invoice.taxes")}}</TD>
                        <TD class="summaryAmount">{{Formatter::currency($totalTaxes)}}</TD>
                    </TR>
                    <TR>
                        <TD class='title'>{{utrans("invoice.credits")}}</TD>
                        <TD class="summaryAmount">{{Formatter::currency($totalCredits)}}</TD>
                    </TR>
                    <TR>
                        <TD class='title'>{{utrans("invoice.payments")}}</TD>
                        <TD class="summaryAmount">{{Formatter::currency($totalPayments)}}</TD>
                    </TR>
                </table>
                <div id="totalDue" class="clear">
                    <span class="title">
                    {{trans("invoice.totalDue",['date' => Formatter::nonUtcDate($invoice->due_date)],$locale)}}
                </span>
                    <span class="amount">
                    {{Formatter::currency($totalRemainingDue)}}
                </span>
                </div>
            </div>
        </div>
    </div>
    <div class="fold">
        @if($showRemittanceSlip === 1)
        <div id="fold3">
            @if($customerPortalEnabled === 1)
            <div id="customerPortalLine">
                {!! utrans("invoice.payOnline",['portal' => $customerPortalURL]) !!}
            </div>
            @endif @if(file_exists(storage_path("logos/companyLogo.png")))
            <div id="remittanceLogo">
                <img src="/usr/share/sonar/storage/logos/companyLogo.png" style="background-color:white;">
            </div>
            @endif
            <div id="creWindowA">
                {{ strtoupper($company->company_name) }}
                <BR> {!! strtoupper(Formatter::address($company->remittanceAddress[0])) !!}
            </div>
            <div id="detachAndReturn">
                @if($lockboxService) LCC 21 @else {{trans("invoice.pleaseDetachAndReturn",[],$locale)}} @endif
            </div>
            <div id="creAccountInfo">
                {{ $account->id }}
                <BR> {{ strtoupper($account->name) }}
                <BR> {!! strtoupper(Formatter::address($address)) !!}
            </div>
            <div id="amountDueAndEnclosedArea">
                <div class="amountDue">
                    <div class="totalAmountDueText">
                        {{trans("invoice.amountDue",[],$locale)}}
                    </div>
                    <div class="totalAmountDueCurrency">
                        {{Formatter::currency($totalRemainingDue)}}
                    </div>
                </div>
                <div class="amountEnclosed">
                    <div class="amountEnclosedText">
                        {{trans("invoice.amountEnclosed",[],$locale)}}
                    </div>
                    @if($invoice->auto_pay !== null && $invoice->auto_pay_attempts === 0) @if(Carbon\Carbon::createFromFormat("Y-m-d",$invoice->auto_pay)->gte(Carbon\Carbon::now(SonarSystem::get("Timezone"))) && $autoMethod === true && $totalRemainingDue > 0)
                    <div class="autopay">
                        {{trans("invoice.autopayScheduled",[],$locale)}}
                    </div>
                    @endif @endif
                </div>
            </div>
            <div id="remittanceDueDate">
                {{trans("invoice.paymentDueDate",[],$locale)}}
                <div class="actualDate">
                    {{Formatter::nonUtcDate($invoice->due_date)}}
                </div>
            </div>
            <div id="taxId">
                @if(SonarSystem::get("Invoice Tax Identification Number")) {{trans("invoice.taxIdentificationNumber",['number' => SonarSystem::get("Invoice Tax Identification Number")])}} @endif
            </div>
            <div id="checksPayableTo">
                @if(SonarSystem::get("Invoice Checks Payable To")) {{trans("invoice.checksPayableTo",['name' => SonarSystem::get("Invoice Checks Payable To")],$locale)}} @endif
                <BR>{{trans("invoice.invoiceIDWithNumber",['id' => $invoice->id])}}
            </div>
            @if($lockboxService)
            <div id="lockboxArea">
                {{$ocrScanline}}
            </div>
            @endif
        </div>
        @endif
    </div>
</div>