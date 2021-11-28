@extends('layouts.base')

@section('content')

  @if (Route::has('login'))
    @auth
      <form class="nav-item" method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="exit-icon d-flex">
          {{ __('Log Out') }}
          <i class="material-icons font-blue"></i>
        </button>
      </form>
    @else
      <a href="{{ route('login') }}">Log in</a>

      @if (Route::has('register') && false)
        <a href="{{ route('register') }}">Register</a>
      @endif
    @endauth
  @endif

  <a href="{{ url('/calendar') }}">{{ url('/calendar') }}</a>


  Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
@endsection

