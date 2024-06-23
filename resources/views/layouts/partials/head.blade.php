<!doctype html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="description" content="Customer Portal">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>{{config("customer_portal.company_name")}}</title>
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <link rel="stylesheet" href="/assets/fonts/feather/feather.min.css">
      <link rel="stylesheet" href="/assets/libs/flatpickr/dist/flatpickr.min.css">
      <link rel="stylesheet" href="/assets/css/theme.css">
      <link rel="stylesheet" href="/assets/css/select2.css">
      <link rel="stylesheet" href="/assets/css/bootstrap-colorpicker.min.css">
      <link rel="stylesheet" href="/assets/css/Chart.min.css">
      <style nonce="{{ csp_nonce() }}">
         /* .show {
            display: block;
            animation: slideDown 5s ease-out !important;
         }

         @keyframes slideDown {
            from {
               height: 0;
            }
            to {
               height: 100%;
            }
         } */
         @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        .bounce {
            animation: bounce 1s;
        }

        .navbar-toggler-icon {
            display: inline-block;
        }
      </style>
   </head>
