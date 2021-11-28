<?php

namespace App\View\Components;

use App\Library\Csv;
use App\Library\Mdb;
use Illuminate\View\Component;

class TablesList extends Component {

  /**
   * @var array
   */
  private $editedTables;
  private $dbTables;
  private $propTables;

  /**
   * Solid edit table
   */
  private function setEditedTables() {
    $this->editedTables = [
      [
        'dbTable' => 'codes',
        'name' => __('db.codes.table')
      ],
    ];
  }

  private function loadTables() {
    $this->dbTables = Mdb::getTables();
    $this->propTables = array_filter($this->dbTables, function ($table) {
      return stripos($table['dbTable'], 'prop_') !== false;
    });
  }

  private function translateTables(string $typeTable = 'dbTables') {
    /*$props = array_map(function ($prop) use ($main) {
      $setting = $main->getSettings('propertySetting')[$prop['dbTable']] ?? false;
      $setting && $setting['name'] && $prop['name'] = $setting['name'];
      return $prop;
    }, $this->$typeTable);*/
  }

  private function mergeEditedTables(string $typeTable) {
    $this->editedTables = array_merge($this->editedTables, ['z_table' => $this->$typeTable]);
  }

  /**
   * Добавления всех таблиц
   */
  private function addAllDbTables() {
    if (!count($this->dbTables)) return;
    $this->translateTables('dbTables');
    $this->mergeEditedTables('dbTables');
  }

  private function addPropTables() {
    if (!count($this->propTables)) return;
    $this->translateTables('propTables');
    $this->mergeEditedTables('propTables');
  }
  /**
   * add Table from database
   */
  private function addDbTables() {
    $this->loadTables();
    if (config('config.change_database')) $this->addAllDbTables();
    else $this->addPropTables();
  }

  /**
   * merge common array
   */
  private function addCsvTables() {
    $this->editedTables = array_merge($this->editedTables, Csv::scanDirCsv(config('config.csv_path')));
  }

  /**
   * Create a new component instance.
   *
   * @return void
   */
  public function __construct() {
    $this->setEditedTables();
    $this->addDbTables();
    $this->addCsvTables();

    //загрузка //Xml::checkXml($dbTables);
  }

  /**
   * Get the view / contents that represent the component.
   */
  public function render() {
    return view('components.tables-list', [
      'dbTables' => $this->editedTables,
    ]);
  }

}
