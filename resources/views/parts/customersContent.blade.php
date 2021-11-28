<div class="d-flex justify-content-between pb-3" id="actionBtnWrap">
  <div>
    <input type="button" class="btn btn-success" value="Добавить" data-action="addCustomer">
    <input type="button" class="btn btn-warning" value="Изменить" data-action="changeCustomer">
  </div>
  <div>
    <input type="button" class="btn btn-danger" value="Удалить" data-action="delCustomer">
  </div>
</div>
<div class="d-none pb-3" id="confirmField">
  <input type="button" class="btn btn-success" value="Подтвердить" data-action="confirmYes">
  <input type="button" class="btn btn-warning" value="Отмена" data-action="confirmNo">
</div>
<div class="res-table">

  <div class="input-group">
    <span class="input-group-text">Поиск:</span>
    <input type="text" id="search" class="form-control" value="" autocomplete="off">
  </div>

  @isset($columns)
    <table id="customersTable" class="text-center table table-striped">
      <thead>
      <tr>
        <th></th>
        @foreach ($columns as $item)
          <th>
            <input type="button" class="btn btn-info btn-sm table-th"
                   value="{{ __('db.customers.' . $item) }}"
                   data-column="{{ $item }}">
          </th>
        @endforeach
      </tr>
      </thead>
      <tbody>
      <tr>
        <td><input type="checkbox" class="" data-id="${id}"></td>
        @foreach ($columns as $item)
          <td>${{!! $item !!}}</td>
        @endforeach
      </tr>
      </tbody>
      <tfoot>
      <tr></tr>
      </tfoot>
    </table>
  @endif
</div>
<div id="paginator" class="w-100"></div>
