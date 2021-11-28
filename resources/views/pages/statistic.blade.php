@extends('layouts.base')

@section('cssLinks')
<link rel="stylesheet" href="{{ asset('public/css/module/statistic.css') }}">
@endsection

@section('jsLinks')
<script type="module" src="{{ asset('public/js/module/statistic.js') }}"></script>
@endsection

@section('content')
<div class="statistic" id="statistic" style="margin: 0 auto;min-height: 500px"></div>
@endsection
