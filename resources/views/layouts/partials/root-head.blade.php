<!DOCTYPE html>
<html lang="{{$language}}">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="Customer Portal">
      <title>{{config("customer_portal.company_name")}}</title>
      <link rel="stylesheet" media="all" href="/assets/css/theme-root.css?v=1.0.1">
      <style>
         .show {
            display: block;
            animation: slideDown 0.3s ease-out !important;
         }

         @keyframes slideDown {
            from {
               height: 0;
            }
            to {
               height: 100%;
            }
         }
      </style>
   </head>