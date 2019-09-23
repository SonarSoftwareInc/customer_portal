@extends('layouts.full')
@section('content')
<div class="container-fluid">
<br>                 
<div class="card">
<div class="card-body">
   <div class="mb-3">
      <div class="row">
         <div class="col ml-n2">
            <h4 class="card-title mb-1">
               {{$ticket->getSubject()}}
            </h4>
            <hr>
            @if(count($replies) > 0)
            @foreach($replies as $reply)
            <div class="row">
               <div class="col ml-n2">
                  @if($reply->incoming == true)
                  <div class="comment-right mb-4">
                     <div class="comment-body-sent">
                        <div class="row">
                           <div class="col">
                              <h5 class="comment-title">
                                 {{utrans("tickets.youWrote")}}
                              </h5>
                           </div>
                           <div class="col-auto">
                              <time class="comment-time-light">
                              {{Formatter::datetime($reply->created_at, true)}} <i class="fe fe-clock ml-1"></i>
                              </time>
                           </div>
                        </div>
                        @else
                        <div class="comment-left mb-4">
                           <div class="comment-body">
                              <div class="row">
                                 <div class="col">
                                    <h5 class="comment-title">
                                       {{utrans("tickets.ispWrote",['companyName' => Config::get("customer_portal.company_name")])}}
                                    </h5>
                                 </div>
                                 <div class="col-auto">
                                    <time class="comment-time-dark">
                                    {{Formatter::datetime($reply->created_at, true)}} <i class="fe fe-clock ml-1"></i>
                                    </time>
                                 </div>
                              </div>
                              @endif
                              <div class="comment-text">
                                 {!! $reply->text !!}
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  @endforeach
                  @endif
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="card">
      <div class="card-body">
         {!! Form::open(['action' => ['TicketController@postReply', 'tickets' => $ticket->getTicketID()], 'id' => 'replyForm', 'method' => 'post']) !!}
         <div class="form-group">
            {!! Form::textarea("reply",null,['class' => 'form-control', 'id' => 'reply', 'placeholder' => utrans("tickets.postAReplyPlaceholder")]) !!}
         </div>
         <button type="submit" class="btn btn-outline-primary">{{utrans("actions.postReply")}}</button>
         {!! Form::close() !!}
      </div>
   </div>
</div>
@endsection
@section('additionalCSS')
@endsection
@section('additionalJS')
<script type="text/javascript" src="/assets/libs/js-validation/jsvalidation.min.js"></script>
{!! JsValidator::formRequest('App\Http\Requests\TicketReplyRequest','#replyForm') !!}
@endsection
