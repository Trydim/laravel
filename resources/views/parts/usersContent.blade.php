<div class="d-flex justify-content-between pb-4" id="actionBtnWrap">
  <div>
    <input type="button" class="btn btn-success" value="Добавить" data-action="addUser">
    <input type="button" class="btn btn-warning" value="Изменить" data-action="changeUser">
    <input type="button" class="btn btn-warning" value="Сменить пароль" data-action="changeUserPassword">
  </div>
  <div>
    <input type="button" class="btn btn-danger" value="Удалить" data-action="delUser">
  </div>
</div>
<div class="text-center d-none" id="confirmField">
  <select id="selectPermission" class="d-none">
    @foreach($permission as $item)
      <option value="{{ $item['id']}}">{{ __($item['name']) }}</option>
    @endforeach
  </select>
  <input type="button" class="btn btn-success" value="Подтвердить" data-action="confirmYes">
  <input type="button" class="btn btn-warning" value="Отмена" data-action="confirmNo">
</div>
<div class="res-table">
<table id="usersTable" class="text-center table table-striped">
	<thead>
	<tr>
    <th></th>
    @isset($columns)
      @foreach ($columns as $item)
        <th>
          <input type="button" class="btn btn-info btn-sm table-th"
                 value="{{ __('db.users.' . $item) }}"
                 data-column="{{ $item }}">
        </th>
      @endforeach
    @endif
	</tr>
	</thead>
	<tbody>
  <tr>
    <td><input type="checkbox" class="" data-id="${id}"></td>
    @isset($columns)
      @foreach ($columns as $item)
        <td>${{!! $item !!}}</td>
      @endforeach
    @endif
  </tr>
  </tbody>
	<tfoot><tr></tr></tfoot>
</table>
</div>
