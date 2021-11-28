<aside class="sidebar" id="sideLeft"> <!-- data-background-color="white"-->
  <div class="position-sticky top-0">
    <ul id="sideMenu" class="sidebar-menu show">
      <li class="nav-label">Main Menu</li>
      @if (Route::has('public'))
        <li>
          <a class="nav-item" href="{{ route('public') }}" aria-expanded="false">
            <i class="pi pi-globe"></i>
            <span class="nav-text">Калькулятор</span>
          </a>
        </li>
      @endif
      @foreach (config('config.access_menu') as $item)
        @if (Route::has($item))
          @switch($item)
            @case('orders')
            <li>
              <a class="nav-item" href="{{ route('orders') }}" aria-expanded="false">
                <i class="pi pi-inbox"></i>
                <span class="nav-text">Заказы</span>
              </a>
            </li>
            @break
            @case('calendar')
            <li>
              <a class="nav-item" href="{{ route('calendar') }}" aria-expanded="false">
                <i class="pi pi-table"></i>
                <span class="nav-text">Календарь</span>
              </a>
            </li>
            @break
            @case('customers')
            <li>
              <a class="nav-item" href="{{ route('customers') }}" aria-expanded="false">
                <i class="pi pi-dollar"></i>
                <span class="nav-text">Клиенты</span>
              </a>
            </li>
            @break
            @case('users')
            <li>
              <a class="nav-item" href="{{ route('users') }}" aria-expanded="false">
                <i class="pi pi-users"></i>
                <span class="nav-text">Менеджеры</span>
              </a>
            </li>
            @break
            @case('statistic')
            <li>
              <a class="nav-item" href="{{ route('users') }}" aria-expanded="false">
                <i class="pi pi-chart-line"></i>
                <span class="nav-text">Статистика</span>
              </a>
            </li>
            @break
            @case('adminDb')
            <li>
              <x-tables-list/>
            </li>
            @break
            @case('catalog')
            <li>
              <a class="nav-item" href="{{ route('catalog') }}" aria-expanded="false">
                <i class="pi pi-user"></i>
                <span class="nav-text">Каталог</span>
              </a>
            </li>
            @break
            @case('fileManager')
            <li>
              <a class="nav-item" href="{{ route('fileManager') }}" aria-expanded="false">
                <i class="pi pi-folder-open"></i>
                <span class="nav-text">Файловый менеджер</span>
              </a>
            </li>
            @break
          @endswitch
        @endif
      @endforeach

      @if (auth()->guard())
        <li>
          <a class="nav-item" href="{{ route('setting') }}" aria-expanded="false">
            <i class="pi pi-sliders-h"></i>
            <span class="nav-text">Настройки</span>
          </a>
        </li>
      @endif
    </ul>
  </div>
</aside>
