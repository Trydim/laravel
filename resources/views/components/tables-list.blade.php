@php
$adminMenu = '';

if(isset($dbTables) && is_array($dbTables)) {

  function subSideMenuItem($fileName, $name, $active) {
    $sitePath = route('adminDb') . $fileName;
    return <<<item
      <li>
        <a class="nav-item pl-5 $active" href="$sitePath">
          <i class="pi pi-file-excel"></i>
          <span class="nav-text">$name</span>
        </a>
      </li>
item;
  }

  function subSideMenu($title, $items, $root) {
    $icon = $root ? 'pi-book' : 'pi-folder';
    $idWrap = $root ? 'id="DBTablesWrap"' : '';
    return <<<menu
      <span class="nav-item has-arrow" role="button" aria-expanded="false">
        <i class="pi $icon"></i>
        <span class="nav-text">$title</span>
      </span>
      <ul aria-expanded="false" class="ms-3 overflow-hidden" $idWrap data-role="link" style="height: 0">$items</ul>
menu;
  }

  function createMenu($title, $tables, $link = '', $root = true) {
    $items = '';
    foreach ($tables as $key => $item) {
      if (!is_numeric($key)) {
        $items .= '<li>' . createMenu($key, $item, $link . '/' . $key, false) . '</li>';
        continue;
      }

      global $tableActive;
      $linkTarget = $item['fileName'] ?? $item['dbTable'] ?? $item['name'];
      $active = request()->path() ===  'adminDb' . $link . '/' . $linkTarget ? 'active' : '';
      $items .= subSideMenuItem($link . '/' . $linkTarget, $item['name'], $active);
    }

    return subSideMenu(__($title), $items, $root);
  }

  $adminMenu = createMenu('Администрирование', $dbTables);
}


echo $adminMenu ?? '';
@endphp
@empty($adminMenu)
  <a class="nav-item" href="{{ route('adminDb') }}" aria-expanded="false">
    <i class="pi pi-user"></i>
    <span class="nav-text">Администрирование</span>
  </a>
@endif
