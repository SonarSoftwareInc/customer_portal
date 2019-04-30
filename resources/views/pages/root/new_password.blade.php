@extends('layouts.root')
@section('content')
<body class="page-login">
   <div class="wrapper">
      <section id="main" class="section content animated fadeInDown delayed_02s">
         <a>
         <img class="logo-form" src="/assets/img/logo.png">
         </a>
         <h1 class="fake-half">{{trans("headers.newPassword",[],$language)}}</h1>
         <p>{{trans("register.resetDescription",[],$language)}}</p>
         {!! Form::open(['action' => ['AuthenticationController@updateContactWithNewPassword', 'token' => $passwordReset->token], 'id' => 'passwordResetForm', 'method' => 'post']) !!}
         <div class="label label-text">
            <label for="input-email">{{trans("register.email",[],$language)}}</label>
            {!! Form::email("email",null,['id' => 'email', 'placeholder' => trans("register.email",[],$language)]) !!}
         </div>
         <div class="label label-text">
            <label for="input-password">{{trans("register.password",[],$language)}}</label>
            {!! Form::password("password",['id' => 'password', 'placeholder' => trans("register.password",[],$language)]) !!}
         </div>
         <div class="label label-text">
            <label for="input-password">{{trans("register.confirmPassword",[],$language)}}</label>
            {!! Form::password("password_confirmation",['id' => 'password_confirmation', 'placeholder' => trans("register.confirmPassword",[],$language)]) !!}
         </div>
         <div class="half vcenter label">
            <div><button type="submit" value="{{trans("actions.resetPassword",[],$language)}}">{{trans("actions.resetPassword",[],$language)}}</button></div>
         </div>
         <small><a href="{{action("AuthenticationController@index")}}">{{trans("register.back",[],$language)}}</a></small>
         {!! Form::close() !!} 
      </section>
   </div>
</body>
@endsection
@section('additionalJS')
<script type="text/javascript" src="/assets/libs/js-validation/jsvalidation.min.js"></script>
{!! JsValidator::formRequest('App\Http\Requests\PasswordUpdateRequest','#passwordResetForm') !!}
@endsection
