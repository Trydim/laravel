<?php

namespace App\Library;

use \Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Mdb {

  private static $columnsParam = [
    'COLUMN_NAME AS columnName',
    'COLUMN_TYPE AS type',
    'COLUMN_KEY AS key',
    'EXTRA AS extra',
    'IS_NULLABLE AS null'
  ];

  /**
   * @return string
   */
  private static function getDbName(): string {
    return config('database.connections.' . config('database.default') . '.database');
  }

  private static function getModelName($str): string {
    return '\App\Models\\' . substr(ucfirst($str), 0, strlen($str) - 1);
  }
  // MAIN query
  //------------------------------------------------------------------------------------------------------------------

  /**
   * @param string $dbTable
   * @param array $select
   * @param array $filters - array [n x 2/3] items size
   * @return array
   */
  static function loadTable(string $dbTable, array $select = ['*'], array $filters = []): array {
    if ($select !== ['*']) {
      $select = array_map(function ($column) {
        return Str::snake($column) . " as $column";
      }, $select);
    }

    $query = DB::table($dbTable)->select($select);
    foreach ($filters as $filter) {
      $query->where($filter);
    }
    return $query->get()->all();
  }

  static function createTable($dbTable, $param) {
    $sql = "CREATE TABLE $dbTable (
            `id` int(10) UNSIGNED NOT NULL,
            `name` varchar(255) NOT NULL DEFAULT 'NoName'";

    //if (get_class_vars($param)) {
      function getParam($prop, $type) {
        $str = ", `$prop` ";

        switch ($type) {
          case 'file': return ", `$prop". "_id` varchar(255)";
          case 'string': return $str . "varchar(255)";
          case 'textarea': return $str . "varchar(1000)";
          case 'double': return $str . "double NOT NULL DEFAULT 1";
          case 'money': return $str . "decimal(10,4) NOT NULL DEFAULT 1.0000";
          case 'date': return $str . "timestamp";
          case 'bool': return $str . "int(1) NOT NULL DEFAULT 1";
        }
      }

      foreach ($param as $prop => $type) {
        $sql .= getParam($prop, $type);
      }
    //}

    DB::unprepared($sql . ')');
    DB::unprepared("ALTER TABLE `$dbTable` ADD PRIMARY KEY (`id`)");
    DB::unprepared("ALTER TABLE `$dbTable` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1");
  }

  /**
   * @param string $like
   * @return mixed|null
   */
  static function getTables(string $like = '') {
    $list = array_map('reset', DB::select('SHOW TABLES'));

    if (!empty($like)) {
      $list = array_filter($list, function ($table) use ($like) {
        return preg_match("/$like/", $table);
      });
    }

    return array_reduce($list, function ($acc, $item) {
      $acc[] = [
        'dbTable' => $item,
        'name'    => str_replace('prop_', '', $item),
      ];
      return $acc;
    }, []);
  }

  /**
   * get columns table
   * @param $dbTable
   *
   * @return mixed
   */
  static function getColumnsByTable($dbTable) {
     return DB::table('INFORMATION_SCHEMA.COLUMNS')
              ->select(self::$columnsParam)
              ->where('TABLE_SCHEMA', self::getDbName())
              ->where('TABLE_NAME', $dbTable)
              ->get()->all();
  }

  /**
   * @param string $dbTable
   * @param string $filters
   *
   * @return integer
   */
  static function getCountRows(string $dbTable, string $filters = ''): int {
    $db = DB::table($dbTable)->count();

    $sql= '';
    if (strlen($filters)) $sql .= ' WHERE ' . $filters;

    $result = self::getRow($sql);

    if (count($result)) return $result['count'];
    return 0;
  }

  static function getLastID($table): int {
    $res = DB::table('INFORMATION_SCHEMA.TABLES')
             ->select('AUTO_INCREMENT as id')
             ->where('TABLE_SCHEMA', self::getDbName())
             ->where('TABLE_NAME', $table)
             ->get();

    if (count($res) === 1) return intval($res[0]->id);
    return -1;
  }

  static function insert($dbTable, $param) {
    //$dbClass = self::getModelName($dbTable);
    //$dbModel = new $dbClass();
    //$key = $dbModel->getKeyName();
    /*foreach ($row as $column => $value) {
        if ($key !== 'id' && $key === $column) {
          $dbModel->$key = $value;
          continue;
        }
      }*/

    foreach ($param as $row) {
      DB::table($dbTable)->insert($row);
    }
  }

  static function update($dbTable, $param) {
    $columns = self::getColumnsByTable($dbTable);
    $keyColumn = array_filter($columns, function ($column) { return $column->key === 'PRI'; })[0];

    foreach ($param as $id => $row) {
      DB::table($dbTable)
        ->where($keyColumn->columnName, $id)
        ->update($row);
    }
  }

  static function delete($dbTable, $param) {
    $columns = self::getColumnsByTable($dbTable);
    $keyColumn = array_filter($columns, function ($column) { return $column->key === 'PRI'; })[0];

    foreach ($param as $id) {
      DB::table($dbTable)
        ->where($keyColumn->columnName, $id)
        ->delete();
    }
  }

}
