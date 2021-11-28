{{--  --}}
<script>
  window.DEBUG         = '{{ config('app.debug') }}';
  window.CSV_DEVELOP   = '{{ config('config.csv_develop') }}';
  window.CURRENT_PAGE  = '{{ Route::getCurrentRoute()->getName() }}';
  window.SITE_PATH     = '{{ request()->getBasePath() ?? '/' }}';
  window.MAIN_PHP_PATH = '{{ route(Route::getCurrentRoute()->getName()) }}-query';
  window.PUBLIC_PAGE   = '{{ 'public' }}';
  window.PATH_IMG      = '{{ asset('/public/images/') }}';
  window.AUTH_STATUS   = '{{ auth()->guard()->check() }}';
  window.TOKEN         = '{{ csrf_token() }}';
</script>
