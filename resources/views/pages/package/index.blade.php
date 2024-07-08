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
   <div class="mt-5">
      <div class="pricing owl-carousel owl-theme mb-3">
         <div class="card card-pricing text-center px-3 mb-4">
             <span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-primary text-white shadow-sm">Basic</span>
             <div class="bg-transparent card-header pt-4 border-0">
                 <h1 class="h1 font-weight-normal text-primary text-center mb-0" data-pricing-value="15">$<span class="price">30</span><span class="h6 text-muted ml-2">/ per month</span></h1>
             </div>
             <div class="card-body pt-0">
                 <ul class="list-unstyled mb-4">
                     <li>Upload speed: 40Mbps</li>
                     <li>Download speed: 60Mbps</li>
                 </ul>
                 <button type="button" class="btn btn-outline-secondary mb-3" data-bs-toggle="modal" data-bs-target="#purchaseModal">Buy</button>
             </div>
         </div>
         <div class="card card-pricing text-center px-3 mb-4">
             <span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-primary text-white shadow-sm">Premium</span>
             <div class="bg-transparent card-header pt-4 border-0">
                 <h1 class="h1 font-weight-normal text-primary text-center mb-0" data-pricing-value="30">$<span class="price">40</span><span class="h6 text-muted ml-2">/ per month</span></h1>
             </div>
             <div class="card-body pt-0">
                 <ul class="list-unstyled mb-4">
                     <li>Upload speed: 50Mbps</li>
                     <li>Download speed: 70Mbps</li>
                 </ul>
                 <button type="button" class="btn btn-outline-secondary mb-3" data-bs-toggle="modal" data-bs-target="#purchaseModal">Buy</button>
             </div>
         </div>
         <div class="card card-pricing text-center px-3 mb-4">
             <span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-primary text-white shadow-sm">Platinum</span>
             <div class="bg-transparent card-header pt-4 border-0">
                 <h1 class="h1 font-weight-normal text-primary text-center mb-0" data-pricing-value="45">$<span class="price">50</span><span class="h6 text-muted ml-2">/ per month</span></h1>
             </div>
             <div class="card-body pt-0">
                 <ul class="list-unstyled mb-4">
                     <li>Upload speed: 60Mbps</li>
                     <li>Download speed: 80Mbps</li>
                 </ul>
                 <button type="button" class="btn btn-outline-secondary mb-3" data-bs-toggle="modal" data-bs-target="#purchaseModal">Buy</button>
             </div>
         </div>
         <div class="card card-pricing text-center px-3 mb-4">
             <span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-primary text-white shadow-sm">Elite</span>
             <div class="bg-transparent card-header pt-4 border-0">
                 <h1 class="h1 font-weight-normal text-primary text-center mb-0" data-pricing-value="60">$<span class="price">60</span><span class="h6 text-muted ml-2">/ per month</span></h1>
             </div>
             <div class="card-body pt-0">
                 <ul class="list-unstyled mb-4">
                     <li>Upload speed: 70Mbps</li>
                     <li>Download speed: 90Mbps</li>
                 </ul>
                 <button type="button" class="btn btn-outline-secondary mb-3" data-bs-toggle="modal" data-bs-target="#purchaseModal">Buy</button>
             </div>
         </div>
     </div>
   </div>
</div>

<!-- Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header align-items-center">
            <h5 class="modal-title" id="purchaseModalLabel">{{ count($paymentMethods) > 0 ? 'Package Purchase' : 'Package Purchase' }}</h5>
            <button type="button" class="btn-close btn btn-outline-danger btn-sm" data-bs-dismiss="modal" aria-label="Close">x</button>
         </div>
         <form action="#" method="post">
            @csrf
            <div class="modal-body">
               @if(count($paymentMethods) > 0)
               <div class="mb-3">
                  <label for="paymentMethod" class="form-label">Choose your payment method</label>
                  <select class="form-select form-control" id="paymentMethod" required>
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
               @endif
            </div>
            <div class="modal-footer">
               @if(count($paymentMethods) > 0)
                  <button type="submit" class="btn btn-primary">Submit</button>
               @else
                  <a href="{{action([\App\Http\Controllers\BillingController::class, 'createPaymentMethod'],['type' => 'credit_card'])}}" class="btn btn-primary">Add</a>
               @endif
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
         </form>
      </div>
   </div>
</div>
@endsection
@section('additionalJS')
<script type="text/javascript" src="{{ asset('assets/js/jquery-qrcode.min.js') }}" nonce="{{ csp_nonce() }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/owlcarousel.min.js') }}" nonce="{{ csp_nonce() }}"></script>
<script nonce="{{ csp_nonce() }}">
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

        owl.trigger('to.owl.carousel', [2]);
    });
</script>
@endsection
