<?php

class Db{

  // MAIN query
  //------------------------------------------------------------------------------------------------------------------

  /**
   * Проверка таблицы перед добавлениями/изменениями
   *
   * @param $curTable
   * @param $dbTable
   * @param $param - link
   * @return array $result
   */
  public function checkTableBefore($curTable, $dbTable, &$param) {
    $result = [];

    array_map(function ($col) use (&$result, $dbTable, &$param) {
      // Если автоинкремент -> удалить все поля из $param
      if ($col['key'] === 'PRI' && strpos($col['extra'], 'auto') !== false) {

        foreach ($param as $k => $item) {
          if (isset($item[$col['columnName']])) {
            unset($param[$k][$col['columnName']]);
          }
        }

      } // Если ключ или уникальный проверить на уникальность
      else if ($col['key'] === 'PRI' || $col['key'] === 'UNI') {

        foreach ($param as $k => $item) {
          if (isset($item[$col['columnName']])) $queryResult = $this->checkHaveRows($dbTable, $col['columnName'], $item[$col['columnName']]);
          else $queryResult = [];

          if (count($queryResult)) {
            $result[] = [
              'id'         => $queryResult[0],
              'columnName' => $col['columnName'],
              'value'      => $item[$col['columnName']],
              'cause'      => 'UNI'
            ];

            unset($param[$k][$col['columnName']]);
          }
        }
      } // Если поле не может быть пустым
      else if ($col['null'] === 'NO') {
        foreach ($param as $k => $item) {
          if (isset($item[$col['columnName']]) && $item[$col['columnName']] === '') {
            $result[] = [
              'id'         => $k,
              'columnName' => $col['columnName'],
              'cause'      => 'Not Null'
            ];

            unset($param[$k][$col['columnName']]);
          }
        }
      }

      // Приведение типов
      if (strpos($col['type'], 'char') === false) {
        foreach ($param as $k => $item) {
          if (isset($item[$col['columnName']])) {
            preg_match('/\w+(?=\()/', $col['type'], $match);
            if (count($match)) {
              //$item[$col['columnName']] = $this->convertType($match[0], item);
            }
          }
        }
      }

      if (count($param) && isset($k) && count($param[$k]) === 0) unset($param[$k]);
    }, $curTable);

    return $result;
  }

  /**
   * @param $dbTable
   * @param $columnName
   * @param $value
   *
   * @return array|null
   */
  public function checkHaveRows($dbTable, $columnName, $value) {
    return self::getCol('Select * FROM ' . $dbTable . ' WHERE ' . $columnName . ' = :value', [':value' => $value]);
  }

  /**
   * @param $dbTable
   * @param $ids
   *
   * @return array
   */
  public function deleteItem($dbTable, $ids) {
    if (count($ids) === 1) {
      $bean = self::xdispense($dbTable);
      $bean->ID = $ids[0];
      $bean->id = $ids[0];
      self::trash($bean);
    } else {
      $beans = self::xdispense($dbTable, count($ids));

      for ($i = 0; $i < count($ids); $i++) {
        $beans[$i]->ID = $ids[$i];
        $beans[$i]->id = $ids[$i];
      }

      self::trashAll($beans);
    }
    return [];
  }


  /**
   * insert or change rows
   *
   * @param $curTable
   * @param $dbTable
   * @param $param
   * @param bool $change
   *
   * @return array|int
   */
  public function insert($curTable, $dbTable, $param, $change = false) {
    $result = $this->checkTableBefore($curTable, $dbTable, $param);

    $beans = self::xdispense($dbTable, count($param));

    if (count($param) > 0) {

      if ($change) {
        foreach ($curTable as $col) {
          if ($col['key'] === 'PRI') {
            $idColName = $col['columnName'];
            break;
          }
        }
      }

      try {
        if (count($param) === 1) {
          foreach ($param as $id => $item) {

            if ($change) $beans->$idColName = $id;

            foreach ($item as $k => $v) {
              if (isset($idColName) && $idColName === $k) continue;
              $beans->$k = $v;
            }
          }
          self::store($beans);
        } else {

          $i = 0;
          foreach ($param as $id => $item) {

            if ($change) $beans[$i]->$idColName = $id;

            foreach ($item as $k => $v) {
              $beans[$i]->$k = $v;
            }
            $i++;
          }
          self::storeAll($beans);
        }
      } catch (\RedBeanPHP\RedException $e) {
        return [
          'result'   => $result,
          'sqlError' => $e->getMessage(),
        ];
      }
    }

    return $result;
  }

  // Files
  //------------------------------------------------------------------------------------------------------------------

  /**
   * @param mixed $ids - if sting use delimiter ","
   *
   * @return array
   */
  public function getFiles($ids = false) {
    if (is_string($ids)) $ids = explode(',', $ids);
    $filters = $ids ? ' ID = ' . implode(' or ID = ', $ids) : '';
    return $this->selectQuery('files', '*', $filters);
  }

  public function setFiles(&$result, $imageIds) {
    $uploadDir = SHARE_DIR . 'upload/';

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    if (isset($_FILES) && count($_FILES)) {
      foreach ($_FILES as $file) {
        $dbFile = $dbDir . $file['name'];
        $uploadFile = $uploadDir . $file['name'];

        // Проверить все
        if (!$file['size']) continue;

        // Если файл существует
        if (file_exists($uploadFile)) {
          !isset($result['fileExist']) && $result['fileExist'] = [];

          $result['fileExist'][] = $file['name'];
          if (filesize($uploadFile) === $file['size']) {
            $id = $this->selectQuery('files', 'ID', " path = '$dbFile' ");
            if (count($id) === 1) { $imageIds[] = $id[0]; continue;
            } else unlink($uploadFile);

          } else {
            $name = pathinfo($file['name'], PATHINFO_BASENAME) . '_' . rand();
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $file['name'] = $name . $ext;

            $dbFile = $dbDir . $file['name'];
            $uploadFile = $uploadDir . $file['name'];
          }
        }

        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
          $this->insert([], 'files', ['0' => [
            'name'   => $file['name'],
            'path'   => $dbFile,
            'format' => $file['type'],
          ]]);
          $imageIds[] = $this->getLastID('files');
        } else $result['error'] = 'Mover file error: ' . $file['name'];
      }
    }

    return implode(',', $imageIds);
  }

  // Elements
  //------------------------------------------------------------------------------------------------------------------

  public function loadElements($sectionID, $pageNumber = 0, $countPerPage = 20, $sortColumn = 'C.name', $sortDirect = false) {
    $pageNumber *= $countPerPage;

    $sql = "SELECT ID, E.name AS 'E.name', activity, sort, last_edit_date AS 'lastEditDate',
                   C.symbol_code AS 'symbolCode', C.name AS 'C.name'
    FROM elements E
    JOIN codes C on C.symbol_code = E.element_type_code
    WHERE E.section_parent_id = $sectionID
    ORDER BY $sortColumn " . ($sortDirect ? 'DESC' : '') . " LIMIT $countPerPage OFFSET $pageNumber";

    return self::getAll($sql);
  }

  public function searchElements($searchValue, $pageNumber = 0, $countPerPage = 20, $sortColumn = 'C.name', $sortDirect = false) {
    $pageNumber *= $countPerPage;
    $searchValue = str_replace(' ', '%', $searchValue);

    $sql = "SELECT ID, E.name AS 'E.name', activity, sort, last_edit_date AS 'lastEditDate',
                   C.symbol_code AS 'symbolCode', C.name AS 'C.name'
    FROM elements E
    JOIN codes C on C.symbol_code = E.element_type_code
    WHERE E.name LIKE '%$searchValue%'
    ORDER BY $sortColumn " . ($sortDirect ? 'DESC' : '');

    //$countPerPage OFFSET $pageNumber;

    $res = self::getAll($sql);

    return [
      'elements'          => array_slice($res, $pageNumber, $countPerPage),
      'countRowsElements' => count($res)
    ];
  }

  // Options
  //------------------------------------------------------------------------------------------------------------------

  private function setImages($imagesIds) {
    $images = $this->getFiles($imagesIds);
    return array_map(function ($item) {
      $path = findingFile(substr(PATH_IMG , 0, -1), mb_strtolower($item['path']));
      $item['path'] = $path ? $path : $item['path'];
      return $item;
    }, $images);
  }
  private function getAlias($table) {
    $tables = ['codes.', 'money.', 'elements.', 'options_elements.', 'units.'];
    $alias = ['C.', 'M.', 'E.', 'O.', 'U.'];
    $table = str_replace($tables, $alias, $table);
    $cols = ['.id', '.type', '.unit', '.lastDate'];
    $alias = ['.ID', '.element_type_code', '.short_name', '.last_edit_date'];
    return str_replace($cols, $alias, $table);
  }

  /**
   * Для страницы Catalog
   * @param false $elementID
   * @param int $pageNumber
   * @param int $countPerPage
   * @param string $sortColumn
   * @param false $sortDirect
   * @return array|null
   */
  public function openOptions($elementID = false, $pageNumber = 0, $countPerPage = 20, $sortColumn = 'name', $sortDirect = false) {
    $pageNumber *= $countPerPage;

    $sql = "SELECT ID, money_input_id AS 'moneyInputId', money_output_id as 'moneyOutputId',
                   images_ids AS 'images', property,
                   name, unit_id AS 'unitId', last_edit_date AS 'lastEditDate', activity, sort,
                   input_price AS 'inputPrice', output_percent AS 'outputPercent', output_price AS 'outputPrice'
            FROM options_elements";

    if ($elementID) {
      $sql .= " WHERE element_id = $elementID
      		ORDER BY $sortColumn " . ($sortDirect ? 'DESC' : '') . " LIMIT $countPerPage OFFSET $pageNumber";
    }

    $options = self::getAll($sql);

    return array_map(function ($option) {
      // set images
      strlen($option['images']) && $option['images'] = $this->setImages($option['images']);

      return $option;
    }, $options);
  }

  /**
   * Load for calculator
   * @param array  $filter
   * @param int    $pageNumber
   * @param int    $countPerPage
   * @param string $sortColumn
   * @param false  $sortDirect
   * @return array
   */
  public function loadOptions($filter = [], int $pageNumber = 0, $countPerPage = -1) {
    $sql = "SELECT O.ID AS 'id', element_id as 'elementId', 
                   E.element_type_code AS 'type', E.sort AS 'elementSort',
                   O.name AS 'name', U.short_name as 'unit', O.activity AS 'activity',
                   O.sort AS 'sort', O.last_edit_date as 'lastDate', property, images_ids AS 'images',
                   MI.code AS 'moneyInput', MO.code AS 'moneyOutput',
                   input_price AS 'inputPrice', output_percent AS 'outputPercent', output_price AS 'price'
            FROM options_elements O
            JOIN elements E on E.ID = O.element_id
            JOIN money MI on MI.ID = O.money_input_id
            JOIN money MO on MO.ID = O.money_output_id
            JOIN units U on U.ID = O.unit_id
            WHERE (E.activity <> 0 OR O.activity <> 0)";

    // Filter
    if (count($filter)) {
      $filterArr = [];
      foreach ($filter as $k => $values) {
        $k = $this->getAlias(AQueryWriter::camelsSnake($k));
        $values = convertToArray($values);
        $str = '(';
        foreach($values as $index => $v) {
          $index > 0 && $str .= " OR ";
          $str .= "$k LIKE '$v'";
        }
        $filterArr[] = $str . ')';
      }

      $sql .= ' AND ' . implode(' AND ', $filterArr);
      unset($filterArr, $filter, $k, $values, $str, $index);
    }
    // Sorting
    $sql .= ' ORDER BY E.sort, O.sort';
    // Paginate
    if ($countPerPage !== -1) {
      $pageNumber *= $countPerPage;
      $sql .= " LIMIT $countPerPage OFFSET $pageNumber";
    }

    $options = self::getAll($sql);

    return array_map(function ($option) {
      // set images
      if (strlen($option['images'])) {
        $option['images'] = [['path' => PATH_IMG . 'stone/a001_raffia.jpg']];
        //$option['images'] = $this->setImages($option['images']);
      }

      // set property
      $properties = $option['property'] ? json_decode($option['property'], true) : [];
      $option['property'] = [];
      foreach ($properties as $property => $id) {
        $propName = str_replace('prop_', '', $property);
        $option['property'][$propName] = $this->getPropertyTable($id, $property);
      }

      return $option;
    }, $options);
  }

  // Property
  //------------------------------------------------------------------------------------------------------------------

  private function parseSimpleProperty($type, $value) {
    switch ($type) {
      default:
      case 'text':
      case 'textarea': return strval($value);
      case 'number': return floatval($value);
      //case 'date':
      case 'bool': return boolval($value);
    }
  }

  private function getPropertyTable($propValue, $propName) {
    static $propTables, $props;

    if (!$propTables) {
      $props = [];
      // Простые параметры
      if (($setting = getSettingFile()) && isset($setting['propertySetting'])) {
        foreach ($setting['propertySetting'] as $prop => $value) {
          $props[$prop] = array_merge($value, ['simple' => true]);
        }
      }

      // Параметры из таблиц БД
      $propTables = $this->getTables('prop');
      foreach ($propTables as $table) {
        $props[$table['dbTable']] = $this->loadTable($table['dbTable']);

        // TODO временно
        if ($table['dbTable'] === 'prop_brand') {
          $tmp = array_map(function ($it) {
            if($it['logo_ids']) {
              $it['logo_ids'] = $this->setImages($it['logo_ids']);
              $it['logo'] = $it['logo_ids'][0]['path'];
            }
            return $it;
          }, $props[$table['dbTable']]);

          $props[$table['dbTable']] = $tmp;
        }
      }
    }

    if (!isset($props[$propName]) || !is_array($props[$propName]) ) return ['name' => 'Property table error'];

    $prop = $props[$propName];

    if (isset($prop['simple'])) return $this->parseSimpleProperty($prop['type'], $propValue);
    foreach ($props[$propName] as $item) if ($item['ID'] === $propValue) return $item;
    return ['name' => "Prop item: $propValue in $propName - not found!"];
  }

  public function createPropertyTable($dbTable, $param) {
    $sql = "CREATE TABLE $dbTable (
            `ID` int(10) UNSIGNED NOT NULL,
            `name` varchar(255) NOT NULL DEFAULT 'NoName'";

    if (count($param)) {
      function getParam($prop, $type) {
        $str = ", `$prop` ";

        switch ($type) {
          case 'file': return ", `$prop". "_ids` varchar(255)";
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
    }

    $error = self::exec($sql . ')');
    !$error && $error = self::exec("ALTER TABLE `$dbTable`
        ADD PRIMARY KEY (`ID`)");
    return self::exec("ALTER TABLE `$dbTable`
        MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1");
  }

  public function delPropertyTable($dbTables) {
    $propTables = $this->getTables('prop');

    foreach ($propTables as $prop) {
      if (in_array($prop['dbTable'], $dbTables)) {
        $table = $prop['dbTable'];
        self::exec("DROP TABLE `$table`");
      }
    }
  }

  // Orders
  //------------------------------------------------------------------------------------------------------------------

  /**
   * @param int $pageNumber
   * @param int $countPerPage
   * @param string $sortColumn
   * @param bool $sortDirect
   * @param array $dateRange
   * @param array $ids
   *
   * @return array|null
   */
  public function loadOrder($pageNumber = 0, $countPerPage = 20, $sortColumn = 'last_edit_date', $sortDirect = false, $dateRange = [], $ids = []) {
    $pageNumber *= $countPerPage;

    $sql = "SELECT O.ID AS 'O.ID', create_date AS 'createDate',
            last_edit_date AS 'lastEditDate', users.name, C.name as 'C.name', total,
            S.name AS 'S.name'
      FROM orders O
      LEFT JOIN users ON user_id = users.ID
      LEFT JOIN customers C ON customer_id = C.ID
      JOIN order_status S ON status_id = S.ID\n";

    if (count($dateRange)) $sql .= "WHERE O.last_edit_date BETWEEN '$dateRange[0]' AND '$dateRange[1]'\n";
    if (count($ids)) {
      $sql .= "WHERE O.ID = ";
      if (count($ids) === 1) $sql .= $ids[0] . " ";
      else $sql .= implode(' OR O.ID = ', $ids) . " ";
    }

    $sql .= "ORDER BY $sortColumn " . ($sortDirect ? 'DESC' : '') . " LIMIT $countPerPage OFFSET $pageNumber";

    return self::getAll($sql);
  }

  /**
   * load full information order
   * @param $id {string}
   *
   * @return array rows
   */
  public function loadOrderById($id) {

    $sql = "SELECT O.ID AS 'ID', create_date AS 'createDate', last_edit_date AS 'lastEditDate',
                   users.name AS 'name', users.ID AS 'userId',
                   users.contacts AS 'contacts', C.name AS 'C.name', total, S.name AS 'Status', 
                   important_value AS 'importantValue', save_value AS 'saveValue',
                   report_value AS 'reportValue'
            FROM orders O
            LEFT JOIN users ON user_id = users.ID
            LEFT JOIN customers C ON customer_id = C.ID
            JOIN order_status S ON status_id = S.ID
            WHERE O.ID = :id";

    $res = self::getAll($sql, [':id' => $id]);

    if (count($res)) {
      $res = $res[0];
      $res['reportValue'] = gzuncompress($res['reportValue']);
      if (!$res['reportValue']) $res['reportValue'] = false;
    }
    return $res;
  }

  public function changeOrders($columns, $dbTable, $commonValues, $status_id) {
    $param = [];

    array_map(function ($id) use (&$param, $status_id) {
      $param[$id] = [
        'status_id'      => $status_id,
        'last_edit_date' => date('Y-m-d G:i:s'), //нужен триггер
      ];
    }, array_values($commonValues));
    $this->insert($columns, $dbTable, $param, true);
  }

  // Orders Visitors
  //------------------------------------------------------------------------------------------------------------------

  public function saveVisitorOrder($param) {
    $bean = self::xdispense('client_orders');
    //$bean->create_date = date('Y-m-d G:i:s');
    foreach ($param as $key => $value) {
      $bean->$key = $value;
    }
    self::store($bean);
  }

  /**
   * @param int $pageNumber
   * @param int $countPerPage
   * @param string $sortColumn
   * @param bool $sortDirect
   * @param array $dateRange
   * @param array $ids
   *
   * @return array|null
   */
  public function loadVisitorOrder($pageNumber = 0, $countPerPage = 20, $sortColumn = 'create_date', $sortDirect = false, $dateRange = [], $ids = []) {
    $pageNumber *= $countPerPage;

    $sql = "SELECT cp_number, create_date, important_value, total FROM client_orders\n";

    if (count($dateRange)) $sql .= "WHERE create_date BETWEEN '$dateRange[0]' AND '$dateRange[1]'\n";
    if (count($ids)) {
      $sql .= "WHERE ID = ";
      if (count($ids) === 1) $sql .= $ids[0] . " ";
      else $sql .= implode(' OR ID = ', $ids) . " ";
    }

    $sql .= "ORDER BY $sortColumn " . ($sortDirect ? 'DESC' : '') . " LIMIT $countPerPage OFFSET $pageNumber";

    return self::getAll($sql);
  }

  /**
   * @param int $pageNumber
   * @param int $countPerPage
   * @param string $sortColumn
   * @param bool $sortDirect
   * @param array $ids
   *
   * @return mixed
   */
  public function loadCustomers($pageNumber = 0, $countPerPage = 20, $sortColumn = 'name', $sortDirect = false, $ids = []) {
    $pageNumber *= $countPerPage;

    $sql = "SELECT C.ID as 'id', name, ITN, contacts, GROUP_CONCAT(O.ID) as 'orders'
     FROM customers C
     LEFT JOIN orders O on C.ID = O.customer_id\n";

    if (count($ids)) {
      $sql .= "WHERE C.ID = ";
      if (count($ids) === 1) $sql .= $ids[0] . " ";
      else $sql .= implode(' OR C.ID = ', $ids) . " ";
    }

    $sql .= "GROUP BY C.ID\n";

    if ($countPerPage < 1000) $sql .= "ORDER BY $sortColumn " . ($sortDirect ? 'DESC' : '') . " LIMIT $countPerPage OFFSET $pageNumber";

    return self::getAll($sql);
  }

  public function loadCustomerByOrderId($orderIds) {

    $sql = "SELECT C.ID as 'ID', C.name as 'name', ITN, contacts FROM orders 
        LEFT JOIN customers C ON C.ID = orders.customer_id
        WHERE orders.ID = $orderIds";

    return self::getRow($sql);
  }

  // Money
  //--------------------------------------------------------------------------------------------------------------------

  public function getMoney() {
    $queryRes = $this->selectQuery('money');

    $res = [];
    foreach ($queryRes as $item) {
      $item['shortName'] = $item['short_name'];
      unset($item['short_name']);
      $item['lastEditDate'] = $item['last_edit_date'];
      unset($item['last_edit_date']);
      $item['rate'] = floatval($item['rate']);

      $res[$item['code']] = $item;
    }

    return $res;
  }

  public function setMoney($rate) {
    $beans = self::xdispense('money', 1);
    foreach ($rate as $currency) {
      $beans->id = $currency['ID'];
      $beans->rate = $currency['rate'];
      self::store($beans);
    }
  }

  /**
   * get Setting for current user
   *
   * @param $currentUser {string}
   * @param $columns {string}
   *
   * @return mixed
   */
  public function getUserSetting($currentUser = false, $columns = 'customization') {
    if (!$currentUser) {
      global $main;
      $currentUser = $main->getLogin();
    }
    $result = self::getAssocRow("SELECT $columns from users WHERE login = ?", [$currentUser]);

    if (count($result) === 1) {
      if ($columns === 'customization') return json_decode($result[0]['customization']);
      if (count(explode(',', $columns)) === 1) return $result[$columns];
    }
    return json_decode('{}');
  }
}
