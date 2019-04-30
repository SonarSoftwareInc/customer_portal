@extends('layouts.full')
@section('content')
<div class="container-fluid">
   <div class="row justify-content-center">
      <div class="col-12">
         <div class="header mt-md-5">
            <div class="header-body">
               <div class="row align-items-center">
                  <div class="col">
                     <h6 class="header-pretitle">
                        {{utrans("tickets.createNewTicket")}}
                     </h6>
                     <h1 class="header-title">
                      {{utrans("headers.tickets")}}
                     </h1>
                  </div>
                  <div class="col-auto">
                  </div>
               </div>
               <div class="row align-items-center">
                  <div class="col">
                  </div>
               </div>
            </div>
         </div>
         <div class="card">
            <div class="card-body">
               {!! Form::open(['action' => 'TicketController@store', 'id' => 'ticketForm']) !!}
               <div class="form-group">
                  <label for="subject">{{utrans("tickets.subject")}}</label>
                  <div class="input-group input-group-merge">
                     {!! Form::text("subject",null,['class' => 'form-control form-control-prepended', 'id' => 'subject', 'placeholder' => utrans("tickets.subjectLong")]) !!}
                     <div class="input-group-prepend">
                        <div class="input-group-text">
                           <span class="fe fe-message-square"></span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <label for="description">{{utrans("tickets.description")}}</label>
                  {!! Form::textarea("description",null,['class' => 'form-control', 'id' => 'description', 'placeholder' => utrans("tickets.descriptionLong")]) !!}
               </div>
               <button type="submit" class="btn btn-outline-primary">{{utrans("actions.createTicket")}}</button>
               {!! Form::close() !!}
            </div>
         </div>
      </div>
   </div>
</div>
</div>
</div>
@endsection
@section('additionalJS')
<script type="text/javascript" src="/assets/libs/js-validation/jsvalidation.min.js"></script>
{!! JsValidator::formRequest('App\Http\Requests\TicketRequest','#ticketForm') !!}
@endsection
