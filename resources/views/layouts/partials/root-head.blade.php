<!DOCTYPE html>
<html lang="{{$language}}">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="Customer Portal">
      <title>{{Config::get("customer_portal.company_name")}}</title>
      <link rel="stylesheet" media="all" href="/assets/css/theme-root.css">
   </head>