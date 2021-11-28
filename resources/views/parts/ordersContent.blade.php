<div class="d-flex justify-content-between pb-4" id="actionBtnWrap">
  <div>
    <input type="button" class="btn btn-success oneOrderOnly" value="Редактировать" data-action="openOrder">
    <span id="orderBtn">
      <input type="button" class="btn btn-warning" value="Изменить Статус" data-action="changeStatusOrder">
      <input type="button" class="btn btn-primary oneOrderOnly" value="Pdf" data-action="savePdf">
      <input type="button" class="btn btn-primary oneOrderOnly" value="Печать" data-action="printReport">
      <input type="button" class="btn btn-primary oneOrderOnly" value="Отправить на почту" data-action="sendOrder">
    </span>
  </div>
  <div>
    <input type="button" class="btn btn-danger" value="Удалить" data-action="delOrders">
  </div>
</div>
<div class="pb-4 d-none" id="confirmField">
  <select id="selectStatus" class="d-none d-inline-block w-25 form-select" name="statusId"></select>
  <input type="button" class="btn btn-success" value="Подтвердить" data-action="confirmYes">
  <input type="button" class="btn btn-warning ms-1" value="Отмена" data-action="confirmNo">
</div>
<div class="pb-4 d-none" id="printTypeField">
  <input type="button" class="btn btn-primary"
         data-action="printReport" data-type="print"
         value="print">
  <input type="button" class="btn btn-warning" data-action="cancelPrint" value="Отмена">
</div>
@if (config('config.users_orders'))
  <div class="d-flex pb-4" style="justify-content: left">
    <div class="form-check">
      <input class="form-check-input" type="radio" name="orderType" value="order" id="orderTypeO" checked data-action="orderType">
      <label class="form-check-label" for="orderTypeO" title="Заказы сохраненные Менеджерами">
        Сохраненные заказы</label>
    </div>
    <div class="form-check ms-1">
      <input class="form-check-input" type="radio" name="orderType" value="visit" id="orderTypeV" data-action="orderType">
      <label class="form-check-label" for="orderTypeV" title="Уникальные расчеты посетителей">
        Пользовательские заказы</label>
    </div>
  </div>
@endif
<div class="res-table">

  <div class="input-group">
    <span class="input-group-text">Поиск:</span>
    <input type="text" id="search" class="form-control" value="" autocomplete="off">
  </div>

  <table id="commonTable" class="text-center table table-striped"></table>

  @isset($ordersColumns)
    <template id="orderTableTmp">
      <table>
        <thead>
        <tr>
          <th></th>
          @foreach ($ordersColumns as $item)
            <th>
              <input type="button" class="btn btn-info btn-sm table-th"
                     value="{{ __('db.orders.' . $item) }}"
                     data-column="{{ $item }}">
            </th>
          @endforeach
        </tr>
        </thead>
        <tbody>
        <tr>
          <td><input type="checkbox" class="" data-id="${O.ID}"></td>
          @foreach ($ordersColumns as $item)
            <td>${{!! $item !!}}</td>
          @endforeach
        </tr>
        </tbody>
      </table>
    </template>
  @endif

  @if (config('config.users_orders') && isset($ordersVisitorColumns))
    <template id="orderVisitorTableTmp">
      <table>
        <thead>
        <tr>
          <th></th>
          @foreach ($ordersVisitorColumns as $item)
            <th>
              <input type="button" class="btn btn-info btn-sm table-th"
                     value="{{ __('db.orders.' . $item) }}"
                     data-column="{{ $item }}">
            </th>
          @endforeach
        </tr>
        </thead>
        <tbody>
        <tr>
          <td><input type="checkbox" class="" data-id="${cp_number}"></td>
          @foreach ($ordersVisitorColumns as $item)
            <td>${{!! $item !!}}</td>
          @endforeach
        </tr>
        </tbody>
      </table>
    </template>
  @endif
</div>
<div id="paginator" class="w-100"></div>
