@extends('layouts.no_nav')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center pt-6">
        <div class="col-lg-8 col-md-8 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-header-title text-muted">
                        <i class="fe fe-settings mr-3"></i> {{trans("settings.settingsAuth", [])}}
                    </h4>
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'AppConfigController@authenticate', 'id' => 'authForm', 'class' => 'mb-4', 'autocomplete' => 'off']) !!}
                    <div class="row mt-1">
                        <div class="col-12 ">
                            <div class="form-group">
                                <label>
                                    {{trans("settings.authenticationKey", [])}}
                                </label>
                                {!! Form::text("key", null ,['id' => 'key', 'class' => 'form-control', 'placeholder' => '']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row mt-1 mb-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                {{trans("actions.login")}}
                            </button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('additionalJS')
@endsection