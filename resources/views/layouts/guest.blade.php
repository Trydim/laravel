<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('public/css/app.css') }}">

  <!-- Scripts -->
  <script src="{{ asset('public/js/app.js') }}" defer></script>
</head>
<body>
  {{ $slot }}
</body>
</html>
