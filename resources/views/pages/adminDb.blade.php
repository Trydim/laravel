@extends('layouts.base')

@section('cssLinks')
  <link rel="stylesheet" href="{{ asset('public/css/module/adminDb.css') }}">
@endsection

@section('jsLinks')
  <script type="module" src="{{ asset('public/js/module/adminDb.js') }}"></script>
@endsection

<!--if (!DB_TABLE_IN_SIDEMENU) { // Если таблицы не в подменю-->
<!--
section('sideRight')
<ul id="DBTablesWrap"></ul>
<div>
  <h2></h2>
  &lt;!&ndash;<input type="button" value="Скачать CSV" data-dbaction="loadCVS" class="fade">&ndash;&gt;
</div>
endsection-->

@section('content')
<input type="hidden" id="dataTableName" value="{{ $tableName }}">
<div class="d-flex justify-content-around">
  <div class="text-center mr-5">
    <h2 id="tableNameField"></h2>
  </div>
  <div id="btnField">
    <input type="button" class="btn btn-primary d-none" value="Добавить" id="btnAddMore">
    <input type="button" class="btn btn-primary" value="Сохранить" id="btnSave" disabled>
    <input type="button" class="btn btn-primary d-none" value="Обновить Конфиг" id="btnRefresh">
  </div>
  <div id="viewField" class="d-flex" style="justify-content: left">
    <div class="d-none">
      <label title="Удобный для редактирования">
        <input type="radio" name="adminType" value="form" data-action="adminType">
        Режим форм</label>
    </div>
    <div class="ml-1">
      <label title="Редактирования в режиме таблицы">
        <input type="radio" name="adminType" value="table" checked data-action="adminType">
        Режим таблицы</label>
    </div>
    <div class="ml-1 d-none">
      <label title="Настройка режима форм">
        <input type="radio" name="adminType" value="config" data-action="adminType">
        Настройка формы (Для опытных)</label>
    </div>
  </div>
</div>
<div id="insertToDB" style="min-height: 100px"></div>
<div style="position: fixed; bottom: 0; right: 0">
  <input type="button" id="legend" class="btn btn-primary btn-white" value="Помощь">
</div>
@endsection

@section('footerContent')
  @if(empty($tableLegend))
    <template id='dataTableLegend'><div>{{ $tableLegend }}</div></template>
  @endif
  <template id="FormViesTmp">
    <form action="#"></form>
  </template>
  <template id="FormRowTmp">
    <div class="d-flex justify-content-between">
      <p data-field="description" style="width: 30%"></p>
      <div data-field="params" class="d-flex justify-content-around" style="width: 70%"></div>
    </div>
  </template>
  <template id="FormParamTmp">
    <section>
      <div data-type="string" class="w-100 text-center">
        <input type="text" class="w-90">
      </div>
      <div data-type="number" class="w-100 text-center">
        <button type="button" class="w-10 inputChange actionMinus">-</button>
        <input type="number" class="w-70" name="number">
        <button type="button" class="w-10 inputChange actionPlus">+</button>
      </div>
      <div data-type="simpleList" class="w-100 text-center">
        <select class="w-90"></select>
      </div>
      <div data-type="relationTable" class="w-100 text-center">
        <select class="w-90"></select>
      </div>
      <div data-type="checkbox" class="w-100 text-center">
        <input type="checkbox" class="w-90">
      </div>
      <div data-type="color" class="w-100 text-center">
        <input type="color" class="w-90">
      </div>
      <div data-type="textarea" class="w-100 text-center">
        <textarea class="w-100" cols="5"></textarea>
      </div>
    </section>
  </template>
  <template id="tablesListTmp">
    <li><label><input type="radio" name="tablesList" value="${name}">${name}</label></li>
  </template>
  <template id="columnsList">
    <table id="tableTempName">
      <thead>
      <tr id="columnName">
        <th class="text-center">${key} ${columnName} - ${type} (${null})</th>
      </tr>
      </thead>
      <tbody id="columnValue">
      <tr>
        <td><input type="text" name="col_${columnName}[]" data-column="${columnName}" value="${${columnName}}"></td>
      </tr>
      </tbody>
    </table>
  </template>
  <template id="emptyTable">
    <p>Таблица пустая</p>
  </template>
  <template id="btnRow">
    <input type="button" class="btnDel" value="X">
  </template>
  <template id="btnDelCancel">
    <input type="button" value="Отменить" class="">
  </template>
  <template id="rowTemplate">
    <div class="mb-2 border w-100">
      <div>
        <small data-field="id"></small>
        <span data-field="desc"></span>
      </div>
      <div class="d-flex justify-content-around" data-field="params"></div>
    </div>
  </template>
  <template id="rowParamTemplate">
    <div style="cursor: pointer">
      <label>
        <span data-field="key"></span>
        <span data-field="type"></span>
        <input type="checkbox" hidden data-action="editField">
      </label>
    </div>
  </template>
  <template id="editParamModal">
    <form action="#">
      <div data-field="setting" class="d-flex flex-column">
        <div class="d-flex w-100">
          <label>Тип:
            <select class="useToggleOption" name="type" data-action="selectChange">
              <option value="string" data-target="">Строка</option>
              <option value="number" data-target="typeNumber">Число</option>
              <option value="simpleList" data-target="simpleList">Простой Список</option>
              <option value="relationTable" data-target="relationTable">Справочник</option>
              <option value="checkbox" data-target="typeCheckbox">Чекбокс</option>
              <option value="color" data-target="">Цвет</option>
            </select>
          </label>
        </div>
        <div class="d-flex flex-column typeNumber">
          <label>минимум<input type="number" value="0" name="min"></label>
          <label>максимум<input type="number" value="1000000000" name="max"></label>
          <label>шаг<input type="number" value="1" name="step"></label>
        </div>
        <div class="d-flex flex-column simpleList">
          <p>В каждой строке 1 значение!</p>
          <p>ключ=значение.</p>
          <div class="d-fle x justify-center">
            <textarea name="listItems" cols="30" rows="5"></textarea>
          </div>
        </div>
        <div class="relationTable">
          <div class="d-flex justify-content-between">
            <label>Таблица (файл)</label>
            <select name="dbTable" data-field="dbTables" data-action="selectDbTables"></select>
          </div>
          <div class="d-flex justify-content-between">
            <label>Поля зависимостей(колонка)</label>
            <select name="tableCol" data-field="tableCol"></select>
          </div>
          <div class="d-flex justify-content-between">
            <label>Множественный</label>
            <input type="checkbox" name="multiple" value="true">
          </div>
        </div>
      </div>
      <div class="d-flex flex-column typeCheckbox">
        <label>Зависимое поле<input type="text" placeholder="ID зависимого поля" name="relTarget"></label>
        <label>Отображать, если активен<input type="checkbox" checked name="relativeWay"></label>
      </div>
    </form>
  </template>
{!! $dictionary !!}
@endsection
