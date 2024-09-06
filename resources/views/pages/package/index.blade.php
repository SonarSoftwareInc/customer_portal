@extends('layouts.full')
@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
<style nonce="{{ csp_nonce() }}">

.card-pricing.popular {
    z-index: 1;
    border: 3px solid #007bff;
}
.card-pricing .list-unstyled li {
    padding: .5rem 0;
    color: #6c757d;
}
.mt-100 {
   margin-top: 100px;
}
.card-pricing {
   margin: auto;
}
.owl-carousel .owl-item.active.center {
   transition: 0.5s ease-in-out;
}
.owl-nav {
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 100px;
}
.owl-carousel .owl-nav button.owl-next, .owl-carousel .owl-nav button.owl-prev {
    color: #b8b8b8 !important;
    font-size: 35px !important;
}
</style>
<div class="container-fluid">
   <div class="text-center mt-100"><h2>Services</h2></div>
   @if(count($services) > 0)
   <div class="mt-5">
      <div class="pricing owl-carousel owl-theme mb-3">
        @foreach($services as $service)
            <div class="card card-pricing text-center px-3 mb-4">
                <span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-primary text-white shadow-sm">{{ $service['name'] }}</span>
                <div class="bg-transparent card-header pt-4 border-0">
                    <h1 class="h1 font-weight-normal text-primary text-center mb-0" data-pricing-value="15">$<span class="price">{{ number_format(($service['amount']/100),2) }}</span><span class="h6 text-muted ml-2">/ per month</span></h1>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-unstyled mb-4">
                        <li>Upload speed: {{ number_format($service['upload_speed'] / 1000, 2) }}Mbps</li>
                        <li>Download speed: {{ number_format($service['download_speed'] / 1000, 2) }}Mbps</li>
                    </ul>
                    <button type="button" class="btn btn-outline-secondary mb-3" data-bs-toggle="modal" data-bs-target="#purchaseModal" data-service-id="{{ $service['id'] }}">Upgrade</button>
                </div>
            </div>
        @endforeach
     </div>
   </div>
   @else 
   <div class="mt-5 text-center">
        <h2 class="text-danger">No service available</h2>
   </div>
   @endif 
</div>

<!-- Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header align-items-center">
            <h5 class="modal-title" id="purchaseModalLabel">Service Upgrade</h5>
            <button type="button" class="btn-close btn btn-outline-danger btn-sm" data-bs-dismiss="modal" aria-label="Close">x</button>
         </div>
         {!! Form::open(['action' => ['\App\Http\Controllers\BillingController@packageSubscription'], 'id' => 'packageForm', 'method' => 'put']) !!}
            @csrf
            <div class="modal-body">
                <input type="hidden" name="new_service_id" id="new_service_id" value="">
                <input type="hidden" name="account_service_id" id="account_service_id" value="{{$account_service_id}}">
                <div class="text-center">
                    <p class="fw-bold my-3">Are you sure you want to upgrade your service?</p>
                </div>
                {{-- @if(count($paymentMethods) > 0)
                <div class="mb-3">
                    <label for="paymentMethod" class="form-label">Choose your payment method</label>
                    <select class="form-select form-control" id="paymentMethod" name="payment_method" required>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->id }}">
                            {{ $method->type }} (A/C ****{{ $method->identifier }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                    <div class="text-center">
                        <p class="fw-bold my-3">Kindly add a payment method before proceeding</p>
                    </div>
                @endif --}}
            </div>
            <div class="modal-footer">
               {{-- @if(count($paymentMethods) > 0) --}}
                  <button type="submit" class="btn btn-primary">Confirm</button>
               {{-- @else
                  <a href="{{action([\App\Http\Controllers\BillingController::class, 'createPaymentMethod'],['type' => 'credit_card'])}}" class="btn btn-primary">Add</a>
               @endif --}}
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
            {!! Form::close() !!}
      </div>
   </div>
</div>
@endsection
@section('additionalJS')
<script type="text/javascript" src="{{ asset('assets/js/jquery-qrcode.min.js') }}" nonce="{{ csp_nonce() }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/owlcarousel.min.js') }}" nonce="{{ csp_nonce() }}"></script>
<script nonce="{{ csp_nonce() }}">
    document.addEventListener('DOMContentLoaded', function () {
        const purchaseModal = document.getElementById('purchaseModal');
        purchaseModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const serviceId = button.getAttribute('data-service-id');
            const serviceIdInput = purchaseModal.querySelector('input[name="new_service_id"]');
            serviceIdInput.value = serviceId;
        });
    });
    $(document).ready(function() {
        var owl = $(".owl-carousel");
        owl.owlCarousel({
            items: 1,
            loop: true,
            center: true,
            margin: 20,
            dots: true,
            nav: true,
            navText: ["<i class='fe fe-chevron-left'></i>", "<i class='fe fe-chevron-right'></i>"],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1000: {
                    items: 3
                }
            },
            onTranslated: function() {
                $('.owl-item .card.card-pricing').removeClass('popular shadow');
                $('.owl-item .card.card-pricing .card-body .btn').removeClass('btn-primary').addClass('btn-outline-secondary');

                $('.owl-item.active.center .card.card-pricing').addClass('popular shadow');
                $('.owl-item.active.center .card.card-pricing .card-body .btn').removeClass('btn-outline-secondary').addClass('btn-primary');
            }
        });

        owl.trigger('to.owl.carousel', [1]);
    });
</script>
@endsection
