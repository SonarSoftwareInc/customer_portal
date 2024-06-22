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
                        Wi-Fi Management
                     </h1>
                  </div>
                  <div class="col-auto">
                  </div>
               </div>
            </div>
         </div>

         <br>
         <div class="row justify-content-center">
            <div class="col-12 col-xl-6">
               <div class="card">
                  <div class="card-body">
                      <div class="row">
                         <div class="col-12 col-xl-12">
                             <!-- Title -->
                              <div class="d-flex justify-content-between align-items-center mb-4">
                                 <h2>Wi-Fi Management</h2>
                                 <div class="wifi-qrcode"></div>
                              </div>
                              {{-- <h2 class="pb-4">Wi-Fi Management</h2>
                              <div class="wifi-qrcode"></div> --}}
                              <!-- Form -->
                              {!! Form::open(['action' => '\App\Http\Controllers\BillingController@wifiManagement', 'id' => 'wifiForm', 'method' => 'PATCH']) !!}
                                 <div class="mb-4">
                                    <label for="wifi" class="form-label">Wi-Fi Band</label>
                                    <select name="wifi_band" id="wifi" class="form-control form-select">
                                       @if(!empty($wifiData))
                                             <option value="both" selected>Both</option>
                                             @foreach($wifiData as $wifi)
                                                <option value="{{ $wifi['wifi_band'] }}">Personalize - {{ $wifi['wifi_band'] }}</option>
                                             @endforeach
                                       @else
                                             <option value="">No data found</option>
                                       @endif
                                    </select>
                                 </div>
                                 <div class="mb-4">
                                    <label for="ssid" class="form-label">Wi-Fi Name</label>
                                    <input type="text" name="ssid" class="form-control" id="ssid" 
                                          value="{{ !empty($wifiData) ? $wifiData[0]['ssid'] : '' }}" {{ empty($wifiData) ? 'disabled' : '' }}>
                                 </div>
                                 <div class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="text" name="password" class="form-control" id="password" 
                                          value="{{ !empty($wifiData) ? $wifiData[0]['wifi_password'] : '' }}" {{ empty($wifiData) ? 'disabled' : '' }}>
                                 </div>
                                 <div class="text-center">
                                    <button type="button" id="reset-button" class="btn btn-danger w-25 mr-2" hidden>Cancel</button>
                                    <button type="submit" id="edit-button" class="btn btn-success w-25" hidden>Submit</button>
                                 </div>
                              {!! Form::close() !!}
                          </div>
                      </div>
                      <!-- / .row -->
                  </div>
              </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section('additionalJS')
<script type="text/javascript" src="{{ asset('assets/js/jquery-qrcode.min.js') }}" nonce="{{ csp_nonce() }}"></script>
<script nonce="{{ csp_nonce() }}">

   document.addEventListener('DOMContentLoaded', function() {
      const ssidInput = document.getElementById('ssid');
      const passwordInput = document.getElementById('password');
      const submitButton = document.getElementById('edit-button');
      const cancelButton = document.getElementById('reset-button');

      let initialSSID = ssidInput.value.trim();
      let initialPassword = passwordInput.value.trim();

      function toggleSubmitButton() {
            if (
               ssidInput.value.trim() !== '' &&
               passwordInput.value.trim() !== '' &&
               (ssidInput.value.trim() !== initialSSID || passwordInput.value.trim() !== initialPassword)
            ) {
               submitButton.hidden = false;
               cancelButton.hidden = false;
            } else {
               submitButton.hidden = true;
               cancelButton.hidden = true;
            }
      }

      function resetInputsAndToggleButton() {
            ssidInput.value = initialSSID;
            passwordInput.value = initialPassword;
            submitButton.hidden = true;
            cancelButton.hidden = true;
      }

      ssidInput.addEventListener('input', toggleSubmitButton);
      passwordInput.addEventListener('input', toggleSubmitButton);
      cancelButton.addEventListener('click', resetInputsAndToggleButton);

      makeQRCode('qqq', $(".wifi-qrcode"));
      function makeQRCode(qr_text, idClass) {
         let qrparams = {
               render: 'image',
               minVersion: 3,
               mode: Number(0),
               fill: "#797e85",
               background: "#ffffff",
               size: 120,
               left: 0,
               top: 0,
               text: qr_text,
               radius: 0.5,
               label: 'QR Code',
               quiet: 3,
         };
         idClass.html("")
         $(document).ready(function () {
               idClass.qrcode(qrparams);
         });
      }
   });
   
   document.addEventListener('DOMContentLoaded', function () {
        const wifiData = @json($wifiData);
        const wifiSelect = document.getElementById('wifi');
        const ssidInput = document.getElementById('ssid');
        const passwordInput = document.getElementById('password');

        wifiSelect.addEventListener('change', function () {
            const selectedBand = this.value;
            const selectedWifi = wifiData.find(wifi => wifi.wifi_band === selectedBand);

            if (selectedWifi) {
                ssidInput.value = selectedWifi.ssid;
                passwordInput.value = selectedWifi.wifi_password;
            }
        });
    });
</script>
@endsection
