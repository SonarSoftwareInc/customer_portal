@extends('layouts.root')
@section('content')
<body class="page-login">
   <div class="wrapper">
      <section id="main" class="section content animated fadeInDown delayed_02s">
         <img class="logo-form" src="/assets/img/logo.png">
         @if($systemSetting->login_page_message)
            <p>
               {{$systemSetting->login_page_message}}
            </p>
         @endif
         <h1 class="fake-half">{{trans('actions.loginMessage', ['ispName' => config("customer_portal.company_name")],$language)}}</h1>
         {!! Form::open(['action' => 'AuthenticationController@authenticate']) !!}
	     <input type="hidden" name="language" value="{{$language}}">
         <div class="label label-text">
            <label for="input-email">{{trans("root.username",[],$language)}}</label>
            {!! Form::text("username",null,['placeholder' => trans("root.username",[],$language), 'id' => 'username']) !!}
         </div>
         <div class="label label-text">
            <label for="input-password">{{trans("root.password",[],$language)}}</label>
            {!! Form::password("password",['placeholder' => trans("root.password",[],$language), 'id' => 'password']) !!}
         </div>
         <div class="half vcenter label">
            <div>
               <button type="submit">
                  {{trans("actions.login",[],$language)}}
               </button>
            </div>
            <div class="right"><a href="/reset" class="forgot">{{trans("headers.forgotUsernameOrPassword",[],$language)}}</a></div>
         </div>
         <small><a href="{{action("AuthenticationController@showRegistrationForm")}}">{{trans("root.register",[],$language)}}</a></small>
         <form class="form-group">
            <select id="language" name="language" class="form-control languageSelector">
            @foreach(getAvailableLanguages($language) as $key => $value)
            <option value="{{$key}}" @if($language == $key) selected @endif>{{$value}}</option>
            @endforeach
            </select>
         </form>
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
