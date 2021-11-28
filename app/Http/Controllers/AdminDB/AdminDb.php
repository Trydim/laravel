<?php

namespace App\Http\Controllers\AdminDb;

use App\Library\Csv;
use App\Library\Mdb;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AdminDb extends Controller {

  /**
   * @return string csv Path
   */
  static function csvPath(): string {
    return config('config.csv_path');
  }

  //---------------------------------------------------------------------------------------------
  // Action
  //---------------------------------------------------------------------------------------------

  static function showTable(Request $request, string $dbTable): array {
    if (stripos($dbTable, '.csv')) {
      return ['csvValues' => Csv::openCsv($dbTable)];
    } else {

      if (config('config.change_database') && config('config.use_database')) {
        return ['dbValues' => Mdb::loadTable($dbTable)];
      } else {
        return [
          'dbValues' => Mdb::loadTable($dbTable),
          'columns' => Mdb::getColumnsByTable($dbTable),
        ];
      }

    }
  }

  static function saveTable(Request $request, string $dbTable): array {
    $result = [];

    if (pathinfo($dbTable, PATHINFO_EXTENSION) !== 'csv') {
      $added = $request->input('added') ?? false;
      $changed = $request->input('changed') ?? false
      $deleted = $request->input('deleted') ?? false;

      $added && Mdb::insert($dbTable, json_decode($added, true));
      $changed && Mdb::update($dbTable, json_decode($changed, true));
      $deleted && Mdb::delete($dbTable, json_decode($deleted, true));
    } else {
      $csvData = $request->input('csvData');

      if (!empty($csvData)) {
        Csv::saveCsv($dbTable, json_decode($csvData, true));
      }
    }

    return $result;
  }

  static function loadCVS(Request $request, string $dbTable): array {
    //return fileForceDownload();
    return [];
  }

  static function loadFormConfig(Request $request, string $dbTable): array {
    $result = [];

    $filePath = static::csvPath() . '../xml' . str_replace('csv', 'xml', $dbTable);
    if (file_exists($filePath) && filesize($filePath) > 60) {
      $result['csvValues'] = Csv::openCsv($dbTable);
      $result['XMLValues'] = new SimpleXMLElement(file_get_contents($filePath));
    } else $result['error'] = 'File error';

    return $result;
  }

  static function saveXMLConfig(Request $request, string $dbTable): array {
    $result = [];
    $XMLConfig = [];

    $result['error'] = Xml::saveXml($dbTable, json_decode($XMLConfig, true));

    return $result;
  }

  static function loadXmlConfig(Request $request, string $dbTable): array {
    $result = [];
    $filePath = self::csvPath() . '../xml' . str_replace('csv', 'xml', $dbTable);

    if (file_exists($filePath)) {
      if (filesize($filePath) < 60) Xml::createXmlDefault($filePath, substr($dbTable, 1));
      $result['XMLValues'] = new SimpleXMLElement(file_get_contents($filePath));
    }

    return $result;
  }

  static function refreshXMLConfig(Request $request, string $dbTable): array {
    $result = [];

    $filePath = PATH_CSV . '../xml' . str_replace('csv', 'xml', $dbTable);
    Xml::createXmlDefault($filePath, substr($dbTable, 1));
    $result['XMLValues'] = new SimpleXMLElement(file_get_contents($filePath));

    return $result;
  }

  //---------------------------------------------------------------------------------------------
  // Entries
  //---------------------------------------------------------------------------------------------

  /**
   * @param string $tableName
   *
   * @return mixed
   */
  public function index(string $tableName = '') {

    if (empty($tableName)) {
      // Найти первую таблицу и сделать ее активной или редирект
    } else {

    }

    return view('pages.adminDb', [
      'tableName' => pathinfo($tableName, PATHINFO_BASENAME),
      'tableLegend' => 'tableLegend',
      'dictionary' => Utilities::initDictionary(),
    ]);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function query(Request $request): JsonResponse {
    $action = $request->input('dbAction');
    $dbTable = $request->input('dbTable');

    try {
      $result = $this::$action($request, $dbTable);
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
