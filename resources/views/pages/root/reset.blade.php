@extends('layouts.root')
@section('content')
<body class="page-login">
   <div class="wrapper">
      <section id="main" class="section content animated fadeInDown delayed_02s">
         <a>
         <img class="logo-form" src="/assets/img/logo.png">
         </a>
         <h1 class="fake-half">{{trans("headers.forgotUsernameOrPassword",[],$language)}}</h1>
         <p>{{trans("register.forgotDescription",[],$language)}}</p>
         {!! Form::open(['action' => 'AuthenticationController@sendResetEmail', 'id' => 'passwordResetForm', 'method' => 'post']) !!}
         <div class="label label-text">
            <label for="input-email">{{trans("register.email",[],$language)}}</label>
            {!! Form::email("email",null,['id' => 'email', 'placeholder' => trans("register.email",[],$language)]) !!}
         </div>
         <div class="half vcenter label">
            <div><button type="submit" value="{{trans("actions.sendResetEmail",[],$language)}}">{{trans("actions.sendResetEmail",[],$language)}}</button></div>
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
{!! JsValidator::formRequest('App\Http\Requests\SendPasswordResetRequest','#passwordResetForm') !!}
@endsection
