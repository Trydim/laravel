@extends('layouts.base')

@section('jsLinks')
  <script defer type="module" src="{{ asset('public/js/module/orders.js') }}"></script>
@endsection

@section('content')
  @include('parts.ordersContent', $contentParam)
@endsection

@section('footerContent')
<template id="changeStatus">
  <option value="${id}">${name}</option>
</template>
<template id="tableImportantValue">
  <div>${key} - ${value}</div>
</template>
<template id="onePageInput">
  <input type="button" value="${pageValue}" class="ml-1 mr-1 input-paginator" data-action="page" data-page="${page}">
</template>
<template id="sendMailTmp">
  <form class="content-center w-100" action="#" id="authForm">
    <div class="input-group">
      <span class="input-group-text">Почта:</span>
      <input type="text" id="email" class="form-control" value="" name="email">
    </div>
  </form>
</template>
<template id="noFoundSearchMsg">
  <tr><td colspan="15">не найдено</td></tr>
</template>
<!--template id="orderOpenForm">
<div>
  <div>Дата создания - \${create_date}</div>
  <div>Дата редактирования - \${last_edit_date}</div>
  <div>Закачик - \${customer}</div>
  <div>Менеджер - \${name}</div>
  <div>Статус - \${status}</div>
  <div>\${important_value}</div>
  <div>\${report_value}</div>
  <div>\${total}</div>
</div>
</template-->
{!! $dictionary !!}
@include('docs.printTpl')
@endsection




