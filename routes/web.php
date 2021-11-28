<?php

use App\Http\Controllers\AdminDb\AdminDb;
use App\Http\Controllers\Catalog\Catalog;
use App\Http\Controllers\Customers\Customers;
use App\Http\Controllers\Orders\Orders;
use App\Http\Controllers\Setting\Setting;
use App\Http\Controllers\Users\Users;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
  Route::get('/', function () {
    return view('pages.welcome'); // Todo тут ссылку на калькулятор
  })->name('home');

  // Calendar Page ------------------------------------------------------------------------------------------------

  Route::get('/calendar', function () {
    return view('pages.calendar');
  })->name('calendar');

  // Catalog Page ------------------------------------------------------------------------------------------------

  Route::get('/catalog', [Catalog::class, 'index'])
       ->name('catalog');

  Route::match(['get', 'post'],'/catalog-query{param?}', [Catalog::class, 'query'])
       ->where('param', '.*')
       ->name('catalogQuery');

  // Orders Page ------------------------------------------------------------------------------------------------

  Route::get('/orders', [Orders::class, 'index'])
       ->name('orders');

  Route::match(['get', 'post'],'/orders-query{param?}', [Orders::class, 'query'])
       ->where('param', '.*')
       ->name('ordersQuery');

  // AdminDb Page ------------------------------------------------------------------------------------------------

  Route::get('/adminDb/{tablePath?}', [AdminDb::class, 'index'])
    ->where('tablePath', '.*')
    ->name('adminDb');

  Route::match(['get', 'post'],'/adminDb-query{param?}', [AdminDb::class, 'query'])
       ->where('param', '.*')
       ->name('adminDbQuery');

  // Customers Page ------------------------------------------------------------------------------------------------

  Route::get('/customers', [Customers::class, 'index'])
       ->name('customers');

  Route::match(['get', 'post'],'/customers-query{param?}', [Customers::class, 'query'])
       ->where('param', '.*')
       ->name('customersQuery');

  // Users Page ------------------------------------------------------------------------------------------------

  Route::get('/users', [Users::class, 'index'])
       ->name('users');

  Route::match(['get', 'post'],'/users-query{param?}', [Users::class, 'query'])
       ->where('param', '.*')
       ->name('usersQuery');

  // Setting Page ------------------------------------------------------------------------------------------------

  Route::get('/setting', [Setting::class, 'index'])
       ->name('setting');

  Route::match(['get', 'post'], '/setting-query{param?}', [Setting::class, 'query'])
       ->where('param', '.*')
       ->name('settingQuery');

});

require __DIR__ . '/auth.php';
