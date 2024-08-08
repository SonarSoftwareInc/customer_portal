@extends('layouts.full')
@section('content')
<div class="container-fluid">
   <div class="row justify-content-center">
      <div class="col-12">
         <div class="header mt-md-5">
            <div class="header-body">
               <div class="row align-items-center">
                  <div class="col">
                     <!-- Pretitle -->
                     <h6 class="header-pretitle">
                     </h6>
                     <!-- Title -->
                     <h1 class="header-title">
                        {{utrans("headers.myDetails")}}
                     </h1>
                  </div>
                  <div class="col-auto">
                  </div>
               </div>
            </div>
         </div>
         <h6 class="header-pretitle">
            <span class="fe fe-settings"></span> {{utrans("profile.changeYourDetails")}}
         </h6>
         <br>
         {!! Form::open(['action' => '\App\Http\Controllers\ProfileController@update','id' => 'profileForm','method' => 'PATCH']) !!}
         <form class="mb-4">
            <div class="row">
               <div class="col-12">
                  <!-- First name -->
                  <div class="form-group">
                     <!-- Label -->
                     <label>
                        {{utrans("profile.name")}}
                     </label>
                     <!-- Input -->
                     {!! Form::text("name",$contact->getName(),['id' => 'name', 'class' => 'form-control', 'placeholder' => utrans("profile.name--placeholder")]) !!}
                  </div>
               </div>
               <div class="col-12">
                  <!-- Email address -->
                  <div class="form-group">
                     <!-- Label -->
                     <label class="mb-1">
                        {{utrans("profile.emailAddress")}}
                     </label>
                     <!-- Form text -->
                     <small class="form-text text-muted">
                        {{utrans("profile.emailUsedFor")}}
                     </small>
                     <!-- Input -->
                     {!! Form::email("email_address",$contact->getEmailAddress(),['id' => 'email_address', 'class' => 'form-control', 'placeholder' => utrans("profile.emailAddress--placeholder")]) !!}
                  </div>
               </div>
               <div class="col-12 col-md-6">
                  <!-- Phone -->
                  <div class="form-group">
                     <!-- Label -->
                     <label>
                        {{utrans("profile.homePhone")}}
                     </label>
                     <!-- Input -->
                     @if($country === 'US')
                     {!! Form::tel("home_phone",$phoneNumbers[\SonarSoftware\CustomerPortalFramework\Models\PhoneNumber::HOME],['id' => 'home_phone', 'class' => 'form-control mb-3', 'data-mask' => '(000) 000-0000', 'placeholder' => utrans("profile.homePhone--placeholder")]) !!}
                     @else
                     {!! Form::tel("home_phone",$phoneNumbers[\SonarSoftware\CustomerPortalFramework\Models\PhoneNumber::HOME],['id' => 'home_phone', 'class' => 'form-control mb-3', 'placeholder' => utrans("profile.homePhone--placeholder")]) !!}
                     @endif
                  </div>
               </div>
               <div class="col-12 col-md-6">
                  <!-- Phone -->
                  <div class="form-group">
                     <!-- Label -->
                     <label>
                        {{utrans("profile.mobilePhone")}}
                     </label>
                     <!-- Input -->
                     @if($country === 'US')
                     {!! Form::tel("mobile_phone",$phoneNumbers[\SonarSoftware\CustomerPortalFramework\Models\PhoneNumber::MOBILE],['id' => 'mobile_phone', 'class' => 'form-control mb-3','data-mask' => '(000) 000-0000', 'placeholder' => utrans("profile.mobilePhone--placeholder")]) !!}
                     @else
                     {!! Form::tel("mobile_phone",$phoneNumbers[\SonarSoftware\CustomerPortalFramework\Models\PhoneNumber::MOBILE],['id' => 'mobile_phone', 'class' => 'form-control mb-3','placeholder' => utrans("profile.mobilePhone--placeholder")]) !!}
                     @endif
                  </div>
               </div>
               <div class="col-12 col-md-6">
                  <!-- Phone -->
                  <div class="form-group">
                     <!-- Label -->
                     <label>
                        {{utrans("profile.workPhone")}}
                     </label>
                     <!-- Input -->
                     @if($country === 'US')
                     {!! Form::tel("work_phone",$phoneNumbers[\SonarSoftware\CustomerPortalFramework\Models\PhoneNumber::WORK],['id' => 'work_phone', 'class' => 'form-control mb-3', 'data-mask' => '(000) 000-0000', 'placeholder' => utrans("profile.workPhone--placeholder")]) !!}
                     @else
                     {!! Form::tel("work_phone",$phoneNumbers[\SonarSoftware\CustomerPortalFramework\Models\PhoneNumber::WORK],['id' => 'work_phone', 'class' => 'form-control mb-3', 'placeholder' => utrans("profile.workPhone--placeholder")]) !!}
                     @endif
                  </div>
               </div>
               <div class="col-12 col-md-6">
                  <!-- Phone -->
                  <div class="form-group">
                     <!-- Label -->
                     <label>
                        {{utrans("profile.fax")}}
                     </label>
                     <!-- Input -->
                     @if($country === 'US')
                     {!! Form::tel("fax",$phoneNumbers[\SonarSoftware\CustomerPortalFramework\Models\PhoneNumber::FAX],['id' => 'fax', 'class' => 'form-control mb-3','data-mask' => '(000) 000-0000', 'placeholder' => utrans("profile.fax--placeholder")]) !!}
                     @else
                     {!! Form::tel("fax",$phoneNumbers[\SonarSoftware\CustomerPortalFramework\Models\PhoneNumber::FAX],['id' => 'fax', 'class' => 'form-control mb-3','placeholder' => utrans("profile.fax--placeholder")]) !!}
                     @endif
                  </div>
               </div>
               <div class="col-12 com-md-12">
                  <button type="submit" class="btn btn-outline-primary">
                     {{utrans("profile.updateDetails")}}
                  </button>
               </div>
               {!! Form::close() !!}
            </div>
            <!-- / .row -->
            <!-- Divider -->
            <hr class="mt-4 mb-5">
            <!-- Pretitle -->
            <h6 class="header-pretitle">
               <span class="fe fe-lock"></span> {{utrans("headers.changePassword")}}
            </h6>
            <br>
            <div class="row">
               <div class="col-12 col-md-6">
                  {!! Form::open(['action' => '\App\Http\Controllers\ProfileController@updatePassword','id' => 'passwordForm','class' => 'form','method' => 'PATCH']) !!}
                  <!-- Password -->
                  <div class="form-group">
                     <!-- Label -->
                     <label>
                        {{utrans("profile.currentPassword")}}
                     </label>
                     <!-- Input -->
                     {!! Form::password("current_password",['type' => 'password','id' => 'current_password', 'class' => 'form-control']) !!}
                  </div>
                  <div class="form-group">
                     <label>
                        {{utrans("profile.newPassword")}}
                     </label>
                     {!! Form::password("new_password",['type' => 'password','id' => 'new_password', 'class' => 'form-control']) !!}
                  </div>
                  <div class="form-group">
                     <label>
                        {{utrans("profile.newPasswordConfirmed")}}
                     </label>
                     <small class="form-text text-muted">
                        {{utrans("profile.passwordConfirm--helper")}}
                     </small>
                     {!! Form::password("new_password_confirmation",['type' => 'password','id' => 'new_password_confirmation', 'class' => 'form-control']) !!}
                  </div>
                  <button type="submit" class="btn btn-outline-primary">
                     {{utrans("profile.changePassword")}}
                  </button>
               </div>
               {!! Form::close() !!}
            </div>
         </form>
      </div>
   </div>
   <!-- Divider -->
   <hr class="mt-4 mb-5">
   <h6 class="header-pretitle">
      <span class="fe fe-flag"></span> {{trans("general.language",[],$language)}}
   </h6>
   <br>
   <div class="row">
      <div class="col-12 col-md-6 col-lg-2 col-xl-2">
         <form class="form-group">
            <select class="form-control languageSelector">
               @foreach(getAvailableLanguages($language) as $key => $value)
               <option value="{{$key}}" @if($language==$key) selected @endif>{{$value}}</option>
               @endforeach
            </select>
         </form>
      </div>
      </form>
   </div>
</div>
</div>
</div>
@endsection
@section('additionalJS')
<script type="text/javascript" src="/assets/libs/js-validation/jsvalidation.min.js"></script>
{!! JsValidator::formRequest('App\Http\Requests\ProfileUpdateRequest', '#profileForm') !!}
{!! JsValidator::formRequest('App\Http\Requests\UpdatePasswordRequest', '#passwordForm') !!}
@endsection