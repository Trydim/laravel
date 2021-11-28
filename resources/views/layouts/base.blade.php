{{--
  global $main, $target;

  sections:
    -- headContent
    -- pageTitle
    -- $cssLinks
    -- global
    -- sideLeft
    -- pageHeader
    -- content
    -- sideRight
    -- pageFooter
    -- jsLinks - array
    -- footerContent
    -- footerContentBase
--}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  @hasSection('headContent') @yield('headContent') @endif

  <title>
    @hasSection('pageTitle')
      @yield('pageTitle')
    @else
      {{ config('app.name', 'Laravel') }}
    @endif
  </title>
  <link rel="icon" href="{{ asset('/public/favicon.ico') }}">

  <!-- Styles -->
  @if (auth()->guard()->check() || request()->routeIs('login'))
    <link rel="stylesheet" href="{{ asset('/public/css/admin.css') }}">
  @endif

  @hasSection('cssLinks')
    @yield('cssLinks')
  @endif

  @once
    @include('layouts.globalJs')
  @endonce
</head>
<body data-theme-version="light"
      data-layout="vertical"> <!-- dark --><!-- horizontal -->

<div id="preloader">
  <div class="sk-three-bounce">
    <div class="sk-child sk-bounce1"></div>
    <div class="sk-child sk-bounce2"></div>
    <div class="sk-child sk-bounce3"></div>
  </div>
</div>

@sectionMissing('global')
  <main class="main-wrapper mx-auto" id="mainWrapper">
    <div class="nav-header">
      <a href="{{ request()->getBasePath() ?? '/' }}" class="brand-logo">
        <img class="logo-abbr" src="./images/logo.png" alt="">
        <span class="brand-title">title</span>
      </a>

      <div class="nav-control" role="button" data-action="menuToggle" data-bs-toggle="tooltip">
        <div>
          <i class="pi pi-caret-left"></i>
        </div>
      </div>
    </div>
    @hasSection('pageHeader')
      @yield('pageHeader')
    @else
      @include('parts.header')
    @endif


    <div class="container-content">
      @hasSection('sideLeft')
        @yield('sideLeft')
      @else
        @include('parts.sideMenu')
      @endif

      <section class="content-body">
        <div class="px-md-4 pt-md-4 pb-5 h-100">@yield('content')</div>
        @hasSection('pageFooter')
          @yield('pageFooter')
        @else
          @include('parts.footer')
        @endif
      </section>

      @hasSection('sideRight')
        <aside id="right" class="col-md-3 col-lg-2 d-md-block">@yield('sideRight')</aside>
      @endif
    </div>
  </main>
@else @yield('global') @endif

<!--<script src="{{ asset('public/js/app.js') }}" defer></script>-->

<script defer type="module" src="{{ asset('public/js/src.js') }}"></script>
<script defer type="module" src="{{ asset('public/js/main.js') }}"></script>

@hasSection('jsLinks')
  @yield('jsLinks')
@endif

@hasSection('footerContent') @yield('footerContent') @endif
@hasSection('footerContentBase') @yield('footerContentBase') @else @include('parts.footerBase') @endif
</body>
</html>
