<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Customers\Customers;
use App\Library\Mdb;
use App\Library\Utilities;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Orders extends Controller {
  /**
   * @const string
   */
  const TABLE_NAME = 'orders';


  /**
   * @var string[]
   */
  private static $defaultColumns = [
    'id',
    'createDate',
    'lastEditDate',
    'userName',
    'customerName',
    'total',
    'status',
  ];

  /**
   * @var string[]
   */
  private static $defaultUsersColumns = [
    'id',
    'createDate',
    'customerName',
    'total',
  ];

  /**
   * @var string[]
   */
  private static $selectColumns = [
    self::TABLE_NAME . '.id AS id',
    'create_date AS createDate',
    'last_edit_date AS lastEditDate',
    'users.name AS userName',
    'customers.name AS customerName',
    'total',
    'orders_status.name AS status',
  ];

  private static function addChangeCustomer(Request $request) {
    $name = $request->input('name');
    if (empty($name)) return ['error' => 'name empty!'];

    $changeUser = $request->input('changeUser') ?? false;
    if ($changeUser === 'change') Customers::changeCustomer($request);
    else if ($changeUser) return Customers::addCustomer($request);
  }

  /**
   *
   * @param $number
   * @param $reportVal
   * @return false|string
   */
  private static function addCpNumber($number, $reportVal) {
    $reportVal = doHook('addCpNumber', $number, $reportVal);
    return gzcompress($reportVal, 9);
  }

  private static function ordersApply(Order $dbModel, array $param): void {
    foreach ($param as $col => $value) {
      $dbModel->$col = $value;
    }
    $dbModel->save();
  }

  //---------------------------------------------------------------------------------------------
  // Action
  //---------------------------------------------------------------------------------------------

  static function loadOrders(Request $request): array {
    $result = [];
    $sortColumn = $request->input('sortColumn') ?? 'create_date';
    $sortDirect = $request->input('sortDirect') === 'true' ? 'DESC' : 'ASC';
    $pageNumber = intval($request->input('currPage')) ?? 0;
    $countPerPage = intval($request->input('countPerPage')) ?? 20;
    $dateRange    = $request->input('dateRange');
    $pageNumber *= $countPerPage;

    $query = DB::table(self::TABLE_NAME)
               ->select(self::$selectColumns)
               ->leftJoin('users', 'users.id', '=', 'user_id')
               ->leftJoin('customers', 'customers.id', '=', 'customer_id')
               ->join('orders_status', 'orders_status.id', '=', 'status_id');

    if ($dateRange) {
      $dateRange = json_decode($dateRange);
      $query->whereRaw("O.last_edit_date BETWEEN '$dateRange[0]' AND '$dateRange[1]'")
            ->orderBy($sortColumn, $sortDirect);
    } else {
      $query->offset($pageNumber)
            ->limit($countPerPage)
            ->orderBy($sortColumn, $sortDirect);
    }

    $result['orders'] = $query->get()->all();
    $result['countRows'] = DB::table(self::TABLE_NAME)->count();

    return $result;
  }

  static function loadUsersOrders(Request $request): array {
    return [];
  }

  static function saveOrder(Request $request): array {
    $reportVal = $request->input('reportVal');
    if (empty($reportVal)) return ['error' => 'report val empty!'];

    self::addChangeCustomer($request);

    $changeUser = $request->input('changeUser');
    $customerId = $changeUser ? self::addChangeCustomer($request) : $request->input('customerId');

    $idOrder = $request->input('reportVal') ?? false;
    $newOrder = !$idOrder || !is_numeric($idOrder);
    $idOrder = $newOrder ? Mdb::getLastID(self::TABLE_NAME) + 1 : $idOrder;

    $param = [
      'customer_id' => $customerId,
      'user_id'     => 1,// TODO нет не пойдет
      'save_value' => $request->input('saveVal') ?? '',
      'important_value' => $request->input('importantVal') ?? '',
      'report_value' => self::addCpNumber($idOrder, $reportVal),
    ];
    $total = $request->input('orderTotal') ?? $request->input('total');
    !empty($total) && is_finite($total) && $param['total'] = floatval($total);

    $newOrder ? self::ordersApply(new Order(), $param) : self::ordersApply(Order::find($customerId), $param);

    // status_id = ; по умолчанию сохранять из настроек
    $result['customerId'] = $customerId;
    $result['orderID'] = $idOrder;
    return $result;
  }

  static function loadOrder(Request $request) {
    $orderIds = json_decode($request->input('$orderIds'));
    return ['order' => Order::find($orderIds[0])];
  }

  static function deleteOrder(Request $request) {
    $orderIds = json_decode($request->input('$orderIds'));

    foreach ($orderIds as $id) {
      $orderM = Order::find($id);
      $orderM->delete();
    }
  }

  //---------------------------------------------------------------------------------------------
  // Entries
  //---------------------------------------------------------------------------------------------

  public function index() {
    $columns = self::$defaultColumns;
    // Тут загрузить настройки. $managerField = $main->getSettings('managerSetting');

    return view('pages.orders', [
      'contentParam' => [
        'ordersColumns' => $columns,
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
      $result = $this::$action($request) ?? [];
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
