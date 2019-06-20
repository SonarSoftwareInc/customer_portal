@extends('layouts.no_nav')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center pt-6">
            <div class="col-lg-8 col-md-8 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title text-muted">
                            <i class="fe fe-settings mr-3"></i> {{trans("errors.500error", [])}}
                        </h4>
                    </div>
                    <div class="card-body">
                        @if($exception->getMessage() == 'You are not authorized to perform that action')
                            {{trans("errors.apiPermissionsFailure")}}
                        @else
                            An Error has occurred. {{ $exception->getMessage() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('additionalJS')
@endsection
