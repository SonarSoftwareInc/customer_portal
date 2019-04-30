@extends('layouts.root')
@section('content')
<body class="page-login">
   <div class="wrapper">
      <section id="main" class="section content animated fadeInDown delayed_02s">
         <a>
         <img class="logo-form" src="/assets/img/logo.png">
         </a>
         <h1 class="fake-half">{{trans("headers.createYourAccount",[],$language)}}</h1>
         <p>{{trans("register.creationDescription",[],$language)}}</p>
         {!! Form::open(['action' => ['AuthenticationController@createAccount', 'token' => $creationToken->token], 'id' => 'createForm', 'method' => 'post']) !!}
         <div class="label label-text">
            <label for="email">{{trans("register.email",[],$language)}}</label>
            {!! Form::email("email",null,['id' => 'email', 'placeholder' => trans("register.email",[],$language)]) !!}
         </div>
         <div class="label label-text">
            <label for="username">{{trans("register.username",[],$language)}}</label>
            {!! Form::text("username",null,['id' => 'username', 'placeholder' => trans("register.username",[],$language)]) !!}
         </div>
         <div class="label label-text">
            <label for="password">{{trans("register.password",[],$language)}}</label>
            {!! Form::password("password",['id' => 'password', 'placeholder' => trans("register.password",[],$language)]) !!}
         </div>
         <div class="label label-text">
            <label for="password_confirmation">{{trans("register.confirmPassword",[],$language)}}</label>
            {!! Form::password("password_confirmation",['id' => 'password_confirmation', 'placeholder' => trans("register.confirmPassword",[],$language)]) !!}
         </div>
         <div class="half vcenter label">
            <div><button type="submit" value="{{trans("actions.createAccount",[],$language)}}">{{trans("actions.createAccount",[],$language)}}</button></div>
         </div>
         <small><a href="{{action("AuthenticationController@index")}}">{{trans("register.back",[],$language)}}</a></small>
         {!! Form::close() !!} 
      </section>
   </div>
</body>
@endsection
@section('additionalJS')
<script>
window.onbeforeunload = function(e){
    document.getElementById('main').className = 'section content animated fadeOutUp';
}
var passwordStrength = {{Config::get("customer_portal.password_strength_required")}};
</script>
<script type="text/javascript" src="/assets/js/pages/register/register.js"></script>
<script type="text/javascript" src="/assets/libs/js-validation/jsvalidation.min.js"></script>
{!! JsValidator::formRequest('App\Http\Requests\AccountCreationRequest','#createForm') !!}
@endsection
