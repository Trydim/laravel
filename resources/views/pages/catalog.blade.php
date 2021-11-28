{{--
  $properties - custom db prop
  $footerData - input hidden data
--}}
@extends('layouts.base')

@section('cssLinks')
  <link href="https://unpkg.com/primevue@^3/resources/themes/saga-blue/theme.css" rel="stylesheet" />
  <link href="https://unpkg.com/primevue@^3/resources/primevue.min.css" rel="stylesheet" />
@endsection

@section('jsLinks')
  <script type="module" src="{{ asset('public/js/module/catalog.js') }}"></script>
@endsection

@section('content')
  @include('parts.catalogContent')
@endsection

@section('footerContent')
{!! $footerData !!}
@endsection
