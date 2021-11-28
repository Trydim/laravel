@extends('layouts.base')

@section('jsLinks')
  <script defer type="module" src="{{ asset('public/js/module/users.js') }}"></script>
@endsection

@section('content')
  @include('parts.usersContent', $contentParam)
@endsection

@section('pageFooter')
<div id="paginator"></div>
@endsection

@section('footerContent')
<template id="permission">
  <option value="\${id}">\${name}</option>
</template>
<template id="tableContactsValue">
  <div>\${key}: \${value}</div>
</template>
<template id="userForm">
  <form action="#">
    <div class="form-group">
      <label class="w-100">Имя пользователя: <input type="text" class="form-control" name="name"></label>
    </div>
    <div class="form-group">
      <label class="w-100">Доступ:
        <select class="form-control" name="permissionId">
          @foreach($contentParam['permission'] as $item)
            <option value="{{ $item['id']}}">{{ __($item['name']) }}</option>
          @endforeach
        </select>
      </label>
    </div>
    <div class="form-group">
      <label class="w-100">Логин: <input type="text" class="form-control" name="login"></label>
    </div>
    <div class="form-group">
      <label class="w-100">Пароль: <input type="password" class="form-control" name="password"></label>
    </div>
    <div class="form-group">
      <label class="w-100">Телефон: <input type="tel" class="form-control" name="phone"></label>
    </div>
    <div class="form-group">
      <label class="w-100">Почта: <input type="email" class="form-control" name="email"></label>
    </div>
    @if (is_array($managerField))
      @foreach($managerField as $k => $item)
        <div class="form-group managerField">
          <label class="w-100">{{ $item['name'] }}
            @switch($item['type'])
              @case('textarea')
                <textarea name="{{ $k }}" class="form-control"></textarea>
              @break
              @case('text') @case('number') @case('date') @default
                <input type="{{ $item['type'] }}" class="form-control" name="{{ $k }}">
              @break
            @endswitch
          </label>
        </div>
      @endforeach
    @endif
    <div id="changeField">
      <label class="w-100">Активность: <input type="checkbox" name="activity"></label>
    </div>
  </form>
</template>
<template id="userChangePassForm">
  <form action="#">
    <!--label>Старый пароль: <input type="password" name="oldPass"></label-->
    <div class="form-group">
      <label class="w-100">Новый пароль: <input type="password" class="form-control" name="newPass"></label>
    </div>
    <div class="form-group">
      <label class="w-100">Повторить пароль: <input type="password" class="form-control" name="repeatPass"></label>
    </div>
  </form>
</template>
{!! $dictionary !!}
@endsection


