<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\Factory;

class LoadCsvList {
  /**
   * Handle an incoming request.
   *
   * @param \Illuminate\Http\Request $request
   * @param \Closure                 $next
   * @return mixed
   * @throws \Illuminate\Contracts\Container\BindingResolutionException
   */
  public function handle(Request $request, Closure $next) {
    return $next($request);
  }
}
