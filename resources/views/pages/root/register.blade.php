@extends('layouts.root')
@section('content')
<body class="page-login">
   <div class="wrapper">
      <section id="main" class="section content animated fadeInDown delayed_02s">
         <a>
         <img class="logo-form" src="/assets/img/logo.png">
         </a>
         <h1 class="fake-half">{{trans('actions.linkMessage', ['ispName' => config("customer_portal.company_name")],$language)}}</h1>
         {!! Form::open(['action' => 'AuthenticationController@lookupEmail', 'id' => 'emailLookupForm', 'method' => 'post']) !!}
         <div class="label label-text">
            <label for="input-email">{{trans("register.email",[],$language)}}</label>
            {!! Form::email("email",null,['id' => 'email', 'placeholder' => trans("register.email",[],$language)]) !!}
         </div>
         <div class="half vcenter label">
            <div><button type="submit" value="{{trans("actions.lookupEmail",[],$language)}}">{{trans("actions.lookupEmail",[],$language)}}</button></div>
         </div>
         <small><a href="{{action("AuthenticationController@index")}}">{{trans("register.back",[],$language)}}</a></small>
         {!! Form::close() !!} 
      </section>
   </div>
</body>
<script>
window.onbeforeunload = function(e){
    document.getElementById('main').className = 'section content animated fadeOutUp';
}
</script>
@endsection
@section('additionalJS')
<script type="text/javascript" src="/assets/libs/js-validation/jsvalidation.min.js"></script>
{!! JsValidator::formRequest('App\Http\Requests\AuthenticationRequest') !!}
@endsection
