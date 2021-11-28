<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Customers extends Controller {

  /**
   * @var string[]
   */
  private static $defaultColumns = [
    'id',
    'name',
    'itn',
    'contacts',
    'orders',
  ];

  /**
   * @var string[]
   */
  private static $selectColumns = [
    'customers.id AS id',
    'name',
    'itn',
    'contacts',
  ];

  static function customersApply(Customer $customerM, Request $request) {
    $name = $request->input('name');
    if (empty($name)) return ['error' => 'Имя пустое'];

    $customerM->name = $name;
    $customerM->itn = $request->input('itn') ?? '';

    $contacts = [];
    $request->input('phone') && $contacts['phone'] = $request->input('phone');
    $request->input('address') && $contacts['address'] = $request->input('address');
    $request->input('email') && $contacts['email'] = $request->input('email');
    count($contacts) && $customerM->contacts = json_encode($contacts);

    $customerM->save();
    return $customerM->id;
  }

  //---------------------------------------------------------------------------------------------
  // Action
  //---------------------------------------------------------------------------------------------

  static function loadCustomers(Request $request): array {
    $result = [];
    $sortColumn   = $request->input('sortColumn') ?? 'name';
    $sortDirect   = $request->input('sortDirect') === 'true' ? 'DESC' : 'ASC';
    $pageNumber   = intval($request->input('currPage')) ?? 0;
    $countPerPage = intval($request->input('countPerPage')) ?? 20;

    $pageNumber *= $countPerPage;
    $selectColumns = self::$selectColumns;
    $selectColumns[] = DB::raw('GROUP_CONCAT(orders.id) as orders');
    $result['customers'] = DB::table('customers')
                             ->select($selectColumns)
                             ->leftJoin('orders', 'orders.customer_id', '=', 'customers.id')
                             ->offset($pageNumber)
                             ->limit($countPerPage)
                             ->groupBy('customers.id', 'name', 'itn', 'contacts')
                             ->orderBy($sortColumn, $sortDirect)->get();

    $result['countRows'] = DB::table('customers')->count();

    return $result;
  }

  static function addCustomer(Request $request) {
    $name = $request->input('name');
    if (empty($name)) return ['error' => 'Имя пустое'];

    return self::customersApply(new Customer(), $request);
  }

  static function changeCustomer(Request $request) {
    $name = $request->input('name');
    if (empty($name)) return ['error' => 'Имя пустое'];
    $customerId = $request->input('customerId');

    return self::customersApply(Customer::find($customerId), $request);
  }

  static function delCustomer(Request $request) {
    $customerId = $request->input('customerId');

    foreach ($customerId as $id) {
      $userM = Customer::find($id);
      $userM->delete();
    }
  }

  //---------------------------------------------------------------------------------------------
  // Entries
  //---------------------------------------------------------------------------------------------

  public function index() {
    $columns = self::$defaultColumns;
    // Тут загрузить настройки. $managerField = $main->getSettings('managerSetting');

    return view('pages.customers', [
      'contentParam' => [
        'columns' => $columns,
      ],
      'dictionary' => Utilities::initDictionary(),
    ]);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function query(Request $request): JsonResponse {
    $action = $request->input('dbAction');

    try {
      $result = $this::$action($request);
    } catch (\Exception $e) {
      return response()->json([
        'status' => false,
        'error'  => $e,
      ]);
    }

    $result['status'] = true;
    return response()->json($result);
  }
}
