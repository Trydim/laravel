@extends('layouts.base')

@section('global')
<main class="position-fixed h-100 w-100">
  <section class="content-center h-100">
    <div class="authincation-content auth-form col-md-5">
      <h4 class="text-center mb-4"><i class="pi pi-user"></i> {{ __('Авторизация:') }}</h4>
      <form action="{{ route('login') }}" method="POST" id="authForm">@csrf
        <div class="form-group">
          <label><strong>{{ __('Логин') }}</strong></label>
          <input name="login" type="text" class="form-control" required value="{{ old('login') }}">
        </div>

        <div class="form-group mt-1">
          <label for="email"><strong>{{ __('Email') }}</strong></label>
          <input id="email" name="email" type="email" class="form-control"
                 value="{{ old('email') }}" placeholder="{{ __('Email') }}" autocomplete="current-email">
        </div>

        <div class="form-group mt-1">
          <label><strong>{{ __('Password') }}</strong></label>
          <input name="password" type="password" class="form-control"
                 placeholder="{{ __('Password') }}" value="{{ old('email') }}" required autocomplete="current-password">
        </div>

        <div class="form-row d-flex justify-content-between mt-4 mb-2">
          <div class="form-group">
            <div class="form-check ml-2">
              <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
              <label class="form-check-label" for="rememberMe">{{ __('Remember me') }}</label>
            </div>
          </div>

          @if (Route::has('password.request'))
            <div class="form-group">
              <a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
            </div>
          @endif
        </div>

        <div class="text-center">
          <button type="submit" class="btn btn-primary btn-block">{{ __('Войти') }}</button>
        </div>
      </form>
      <div class="new-account mt-3">
        <p>$publicLink</p>
      </div>

    </div>
  </section>
</main>

<p class="position-absolute">error : {{ $errors }}</p>
<p class="position-absolute" style="top: 15px">status: {{ session('status') }}</p><br>
@endsection
