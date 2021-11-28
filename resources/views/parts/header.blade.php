@auth
<div class="header">
  <div class="header-content">
    <div></div>
    <nav class="navbar navbar-expand">
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav">
          <li class="d-flex justify-content-end dropdown header-profile">
            <a class="nav-link" href="#" role="button" data-toggle="dropdown">
              <i class="pi pi-user"></i>
              {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu mt-5">
              <form class="nav-item" method="POST" action="{{ route('logout') }}" id="authBlock">
                @csrf
                <div class="dropdown-item">
                  <button class="exit-icon d-flex">
                    <i class="pi pi-sign-out"></i>
                    <span class="ml-2">{{ __('Log Out') }}</span>
                  </button>
                </div>
              </form>
            </div>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</div>
@else
  @include('parts.authBlock')
@endif
