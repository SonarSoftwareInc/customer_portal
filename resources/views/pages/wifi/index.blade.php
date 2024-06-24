@extends('layouts.full')
@section('content')
<style nonce="{{ csp_nonce() }}">
   .input-group-text {
      border-top-left-radius: 0px;
      border-bottom-left-radius: 0px;
      border-left: white;
   }
</style>
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
                                             @foreach($wifiData as $wifi)
                                                <option value="{{ $wifi['wifi_band'] }}">Personalize - {{ $wifi['wifi_band'] }}</option>
                                             @endforeach
                                             <option value="both" selected>Both</option>
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
                                    <div class="input-group">
                                       <input type="password" name="password" class="form-control" id="password" 
                                          value="{{ !empty($wifiData) ? $wifiData[0]['wifi_password'] : '' }}" {{ empty($wifiData) ? 'disabled' : '' }}>
                                       <span class="input-group-text">
                                           <a href="javascript:void(0)"
                                               class="link-secondary fe fe-eye field-icon toggle-password"
                                               toggle="#password">
                                           </a>
                                       </span>
                                    </div>
                                    {{-- <input type="password" name="password" class="form-control" id="password" 
                                          value="{{ !empty($wifiData) ? $wifiData[0]['wifi_password'] : '' }}" {{ empty($wifiData) ? 'disabled' : '' }}> --}}
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
      
      // Interactive Buttons
      const ssidInput = document.getElementById('ssid');
      const passwordInput = document.getElementById('password');
      const submitButton = document.getElementById('edit-button');
      const cancelButton = document.getElementById('reset-button');

      // Initial SSID and PASSWORD
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

      // Qr Code Generation
      let qr_text = 'WIFI:T:nopass;S:'+initialSSID+';P:'+initialPassword+';H:;;';
      makeQRCode(qr_text, $(".wifi-qrcode"));
      function makeQRCode(qr_text, idClass) {
         let qrparams = {
               render: 'image',
               minVersion: 3,
               mode: Number(0),
               fill: "#797e85",
               background: "#ffffff",
               size: 90,
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

      // password show hide
      $(".toggle-password").click(function () {
         $(this).toggleClass("fe-eye fe-eye-off");
         var input = $($(this).attr("toggle"));
         if (input.attr("type") == "password") {
               input.attr("type", "text");
         } else {
               input.attr("type", "password");
         }
      });

      // Get Wifi Management Data
      const wifiData = @json($wifiData);
      const wifiSelect = document.getElementById('wifi');

      // Get SSID and Password accroding the Wifi Band
      wifiSelect.addEventListener('change', function () {
         const selectedBand = this.value;

         if (selectedBand === 'both') {
            // Set ssidInput and passwordInput values to wifiData[0]'s values
            if (wifiData.length > 0) {
               ssidInput.value = wifiData[0]['ssid'];
               passwordInput.value = wifiData[0]['wifi_password'];
            }
         } else {
            const selectedWifi = wifiData.find(wifi => wifi.wifi_band === selectedBand);

            if (selectedWifi) {
               ssidInput.value = selectedWifi.ssid;
               passwordInput.value = selectedWifi.wifi_password;
            }
         }

         // Generate QR code and toggle submit button
         initialSSID = ssidInput.value;
         initialPassword = passwordInput.value;
         qr_text = 'WIFI:T:nopass;S:' + initialSSID + ';P:' + initialPassword + ';H:;;';
         makeQRCode(qr_text, $(".wifi-qrcode"));
         toggleSubmitButton();
      });
   });
</script>
@endsection
