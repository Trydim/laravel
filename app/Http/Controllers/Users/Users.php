<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class Users extends Controller {

  /**
   * @var string[]
   */
  private static $defaultColumns = [
    'id',
    'permissionName',
    'login',
    'name',
    'email',
    'phone',
    'registerDate',
    'activity'
  ];

  /**
   * @var string[]
   */
  private static $selectColumns = [
    'users.id AS id',
    'permission.id AS permissionId',
    'permission.name AS permissionName',
    'login',
    'users.name AS name',
    'email',
    'phone',
    'created_at AS registerDate',
    'activity'
  ];

  static function changeUserApply($usersId, $authForm, Request $request = null) {
    foreach ($usersId as $id) {
      $userM = User::find($id);

      $custom = [];
      foreach ($authForm as $k => $v) {
        if ($k === 'permissionId') { $userM->permission_id = $v; continue; }
        if (stripos($k, 'custom_') !== false) $custom[$k] = $v;
        $userM->$k = $v;
      }

      count($custom) && $userM->customization = json_encode($custom);
      $userM->activity = intval($request->input('activity'));
      $userM->save();
    }
  }

  //---------------------------------------------------------------------------------------------
  // Action
  //---------------------------------------------------------------------------------------------

  static function loadUsers(Request $request) {
    $result = [];
    $sortColumn   = $request->input('sortColumn') ?? 'created_at';
    $sortDirect   = $request->input('sortDirect') === 'true' ? 'DESC' : 'ASC';
    $pageNumber   = intval($request->input('currPage')) ?? 0;
    $countPerPage = intval($request->input('countPerPage')) ?? 20;

    $pageNumber *= $countPerPage;

    $result['users'] = DB::table('users')
                         ->leftJoin('permission', 'permission.id', '=', 'users.permission_id')
                         ->select(self::$selectColumns)
                         ->offset($pageNumber)
                         ->limit($countPerPage)
                         ->orderBy($sortColumn, $sortDirect)->get();

    $result['countRows'] = DB::table('users')->count();
    //$result['permissionUsers'] = $db->loadTable('permission');

    return $result;
  }

  static function addUser(Request $request) {
    $authForm = json_decode($request->input('authForm'));

    $userM = new User();

    $custom = [];
    foreach ($authForm as $k => $v) {
      if ($k === 'permissionId') { $userM->permission_id = $v; continue; }
      if ($k === 'password') { $userM->password = password_hash($v, PASSWORD_BCRYPT); continue; }
      if (stripos($k, 'custom_') !== false) $custom[$k] = $v;
      $userM->$k = $v;
    }

    count($custom) && $userM->customization = json_encode($custom);
    $userM->activity = 1;
    $userM->save();
  }

  static function changeUser(Request $request) {
    $usersId = json_decode($request->input('usersId'));
    $authForm = json_decode($request->input('authForm'));

    self::changeUserApply($usersId, $authForm, $request);
  }

  static function changeUserPassword(Request $request) {
    $usersId = json_decode($request->input('usersId'));
    $validPass = $request->input('validPass');

    if (count($usersId) === 1) {
      $userM = User::find($usersId[0]);
      $userM->password = password_hash($validPass, PASSWORD_BCRYPT);
      $userM->save();
    }
  }

  static function delUser(Request $request) {
    $usersId = json_decode($request->input('usersId'));

    foreach ($usersId as $id) {
      $userM = User::find($id);
      $userM->delete();
    }
  }

  //---------------------------------------------------------------------------------------------
  // Entries
  //---------------------------------------------------------------------------------------------

  public function index() {
    $permission = Permission::all();

    $columns = self::$defaultColumns;
    $setting = Utilities::getSettingFile(true);
    $managerField = $setting['managerFields'] ?? [];

    return view('pages.users', [
      'contentParam' => [
        'columns'    => $columns,
        'permission' => $permission,
      ],
      'managerField' => $managerField,
      'dictionary'   => Utilities::initDictionary(),
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
