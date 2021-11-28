@extends('layouts.base')

{{--
  $isAdmin - bool
  $contentParam - array
  $properties - array Catalog product properties
--}}

@section('cssLinks')
  <link href="https://unpkg.com/primevue@^3/resources/themes/saga-blue/theme.css" rel="stylesheet" />
  <link href="https://unpkg.com/primevue@^3/resources/primevue.min.css" rel="stylesheet" />
@endsection

@section('jsLinks')
  <script type="module" src="{{ asset('public/js/module/setting.js') }}"></script>
@endsection

@section('content')
  @include('parts.settingContent', $contentParam)
@endsection

@section('footerContent')
<template id="rateModalTmp">
  <table class="text-center table table-striped">
    <thead>
    <tr>
      <th>Код</th>
      <th>Имя</th>
      <th>Курс</th>
      <th>Основная</th>
      <th>Обозначение</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</template>
{!! $footerData !!}
@endsection
