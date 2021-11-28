@extends('layouts.base')

<!--$block = template('parts/authBlock');-->
@section('cssLinks')
<link rel="stylesheet" href="{{ asset('public/css/module/calendar.css') }}">
@endsection

@section('jsLinks')
<script type="module" src="{{ asset('public/js/module/calendar.js') }}"></script>
@endsection

@section('content')
<div class="calendar" id="calendar"></div>
@endsection

@section('footerContent')
<a id="publicPageLink" href="public" hidden></a>
<template id="orderTemplate">
  <div>
    <span>Статус заказа: \${S.name}</span><br>
    <span>Создан: \${createDate}</span><br>
    <span>Посл изменения: \${lastEditDate}</span><br>
    <span>Менеджер: \${name}</span><br>
    <span>Клиент: \${C.name}</span><br>
    <!--<span>\${importantValue}</span><br>-->
  </div>
</template>
<template id="orderBtnTemplate">
  <input type="button" class="btn btn-success" value="Редактировать" data-action="openOrder">
</template>
@endsection
