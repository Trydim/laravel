<?php

namespace App\Http\Controllers\Catalog;

use App\Library\Mdb;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Element;
use App\Models\File;
use App\Models\OptionsElements;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Translation\FileLoader;


class Catalog extends Controller {

  private static $uploadUrl = '/storage/app/';
  /**
   * @var string[]
   */
  private static $defaultColumnsSection = [
    'id AS key',
    'parent_id AS parentId',
    'name AS label',
    'code',
  ];
  /**
   * @var string[]
   */
  private static $defaultColumnsElements = [
    'id',
    'symbol_code AS symbolCode',
    'codes.name AS codeName',
    'elements.name AS name',
    'activity',
    'sort',
    'last_edit_date AS lastEditDate',
  ];
  /**
   * @var string[]
   */
  private static $defaultColumnsOptions = [
    'O.id AS id',
    'images_id AS images',
    'O.name AS name',
    'unit_id AS unitId',
    'units.short_name AS unitName',
    'property',
    'O.activity AS activity',
    'sort',
    'money_input_id AS moneyInputId',
    'mI.short_name AS moneyInputName',
    'input_price AS inputPrice',
    'output_percent AS outputPercent',
    'money_output_id AS moneyOutputId',
    'mO.short_name AS moneyOutputName',
    'output_price AS outputPrice',
    'O.last_edit_date AS lastEditDate',
  ];

  private static function getUploadPath(): string {
    return storage_path('app' . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR);
  }

  /*неиспользуется*/
  private static function prepareData($data) {
    return json_encode(array_map(function ($item) {
      return [$item->id => $item];
    }, $data));
  }

  private static function sectionApply(Section $sectionM, Request $request) {
    $name = $request->input('name');
    if (empty($name)) return ['error' => 'Имя пустое'];

    $sectionM->name = $name;
    $sectionM->code = $request->input('code');
    $sectionM->parent_id = $request->input('parentId');

    $sectionM->save();
    return $sectionM->id;
  }

  private static function newElement($request): array {
    $elementM = new Element();

    $name = $request->input('name');
    if (empty($name)) return ['error' => 'Имя пустое'];
    $parentId = $request->input('parentId');
    if ($parentId === "0") return ['error' => 'Нельзя располагать в родительском разделе!'];
    if ($elementM::where([['name', $name], ['section_parent_id', $parentId]])->count())
      return ['error' => 'Элемент с таким именем существует'];

    $elementM->element_type_code = $request->input('type');
    $elementM->section_parent_id = $parentId;
    $elementM->name     = $name;
    $elementM->activity = $request->input('activity') === 'true';
    $elementM->sort     = $request->input('sort');

    $elementM->save();
    $result['elementId'] = $elementM->id;
    $result['optionId'] = self::newOption($request, $result['elementId']);
    return $result;
  }

  private static function newOption(Request $request, string $elementId = ''): array {
    $optionsM = new OptionsElements();
    $name = $request->input('name');
    $elementId = $elementId ?: $request->input('elementsId');

    if (empty($name)) return ['error' => 'Имя пустое'];
    if ($optionsM::where([['name', $name], ['element_id', $elementId]])->count())
      return ['error' => 'Элемент с таким именем существует!'];

    $optionsM->element_id      = $elementId;
    $optionsM->money_input_id  = $request->input('moneyInputId') ?? 1;
    $optionsM->input_price     = $request->input('inputPrice') ?? 0;
    $optionsM->output_percent  = $request->input('percent') ?? 0;
    $optionsM->money_output_id = $request->input('moneyOutputId') ?? 1;
    $optionsM->output_price    = $request->input('outputPrice') ?? 1;
    $optionsM->unit_id         = $request->input('unitId') ?? 1;
    $optionsM->images_id       = self::setFiles($request);
    $optionsM->name            = $request->input('name');
    $optionsM->property        = self::setProperty($request);
    $optionsM->activity        = $request->input('activity') === 'true';
    $optionsM->sort            = $request->input('sort');

    $optionsM->save();
    $result['optionId'] = $optionsM->id;
    return $result;
  }

  private static function loadProperties(): array {
    $prop = [];
    $propertiesSetting = Utilities::getSettingFile();
    if (!isset($propertiesSetting['propertiesSetting'])) return [];
    $propertiesSetting = $propertiesSetting['propertiesSetting'];

    foreach (Mdb::getTables('prop') as $table) {
      $name = isset($propertiesSetting[$table['dbTable']])
        ? $propertiesSetting[$table['dbTable']]['name']
        : $table['name'];

      $prop[$table['dbTable']] = [
        'name'   => $name,
        'values' => Mdb::loadTable($table['dbTable']),
      ];
    }
    return $prop;
  }
  private static function setProperty(Request $request): string {
    $prop = $request->input('property');
    return '';
  }

  private static function checkChangeFiles(Request $request, &$ids): string {
    if ($request->input('dbAction') === 'createElement') return $ids;

    $id = json_decode($request->input('optionsId'))[0];
    $optionM = OptionsElements::find($id);
    $imagesId = $optionM->images_id;

    /*foreach ($imagesId as $id) {

    }*/
    return $ids;
  }

  private static function setFiles(Request $request): string {
    $uploadPath = self::getUploadPath();

    $ids = array_map(function ($file) use ($uploadPath) {
      $saveName = $name = $file->getClientOriginalName();
      $size = $file->getSize();
      $fileExist = file_exists($uploadPath . $saveName);
      $fileSizeSame = $fileExist && filesize($uploadPath . $saveName) === $size;

      $fileExist && $saveName = uniqid() . $name; // New Name
      if (!($fileExist && $fileSizeSame)) $file->storeAs('upload', $saveName);  // If same name and size, do not save
      else $id = DB::table('files')->select('id')->where('name', $name)->get()->all();

      if (isset($id) && count($id) === 1) return $id[0]->id;

      $fileM = new File();
      $fileM->name   = $name;
      $fileM->path   = 'upload/' . $saveName;
      $fileM->format = pathinfo($name, PATHINFO_EXTENSION);
      $fileM->save();
      return $fileM->id;
    }, $request->file());

    return implode(',', $ids);
  }

  private static function getFile(string $images): array {
    $uploadPath = request()->getBaseUrl() . self::$uploadUrl;

    return array_map(function ($id) use ($uploadPath) {
      $file = File::find($id);
      return [
        'id'   => $id,
        'name' => $file->name,
        'src'  => $uploadPath . $file->path,
      ];
    }, explode(',', $images));
  }

  //---------------------------------------------------------------------------------------------
  // Action
  //---------------------------------------------------------------------------------------------

  /* ------------------- section ------------------- */
  static function loadSection(Request $request): array {
    $result['section'] = DB::table('sections')
      ->select(self::$defaultColumnsSection)
      ->orderBy('parent_id')
      ->get();

    return $result;
  }

  static function openSection(Request $request): array {
    $result = [];
    $sectionId = $request->input('sectionId');

    $result['elements'] = DB::table('elements')
                            ->select(self::$defaultColumnsElements)
                            ->join('codes', 'codes.symbol_code', '=', 'elements.element_type_code')
                            ->where('section_parent_id', '=', $sectionId)
                            ->get();

    $result['countRowsElements'] = DB::table('elements')
                                     ->where('section_parent_id', '=', $sectionId)
                                     ->count();

    return $result;
  }

  static function createSection(Request $request): array {
    return ['sectionId' => self::sectionApply(new Section(), $request)];
  }

  static function changeSection(Request $request): array {
    $sectionId = $request->input('sectionId');
    return ['sectionId' => self::sectionApply(Section::find($sectionId), $request)];
  }

  static function deleteSection(Request $request) {
    $sectionId = $request->input('sectionId');

    if ($sectionId) {
      $sectionM = Section::find($sectionId);
      $sectionM->delete();
    };
  }

  /* ------------------- element ------------------- */
  static function openElement(Request $request): array {
    $result = [];
    $elementId = $request->input('elementsId');

    $options = DB::table('options_elements AS O')
                 ->select(self::$defaultColumnsOptions)
                 ->join('units', 'units.id', '=', 'O.unit_id')
                 ->join('money as mI', 'mI.id', '=', 'O.money_input_id')
                 ->join('money as mO', 'mO.id', '=', 'O.money_output_id')
                 ->where('element_id', '=', $elementId)
                 ->get()->all();

    $result['options'] = array_map(function ($option) {
      !empty($option->images) && $option->images = self::getFile($option->images);

      return $option;
    }, $options);

    $result['countRowsOptions'] = DB::table('options_elements')
                                     ->where('element_id', '=', $elementId)
                                     ->count();

    return $result;
  }

  static function createElement(Request $request): array {
    return self::newElement($request);
  }
  static function copyElement(Request $request): array {
    return self::newElement($request);
  }
  static function changeElements(Request $request): array {
    $elementIds  = json_decode($request->input('elementsId'));
    $fieldChange = json_decode($request->input('fieldChange'));

    $parentId = $request->input('parentId');
    if ($parentId === "0") return ['error' => 'Нельзя располагать в родительском разделе!'];
    $name = $request->input('name');
    if (count($elementIds) === 1 && Element::where([['name', $name], ['section_parent_id', $parentId]])->count() > 1)
      return ['error' => 'Элемент с таким именем существует'];

    $type     = $fieldChange->type ? $request->input('type') : false;
    $parentId = $fieldChange->parentId ? $parentId : false;
    $activityChange = $fieldChange->activity;
    $activityChange && $activity = $request->input('activity') === 'true';
    $sort     = $fieldChange->sort ? $request->input('sort') : false;

    $result['elementIds'] = [];
    foreach ($elementIds as $id) {
      $elementM = Element::find($id);

      $type           && $elementM->element_type_code = $type;
      $parentId       && $elementM->section_parent_id = $parentId;
      $name           && $elementM->name              = $name;
      $activityChange && $elementM->activity          = $activity;
      $sort           && $elementM->sort              = $sort;

      $elementM->save();
      $result['elementIds'][] = $id;
    }

    return $result;
  }
  static function deleteElements(Request $request) {
    $elementIds = json_decode($request->input('elementsId'));



    foreach ($elementIds as $id) {
      $elementM = Element::find($id);
      $elementM->delete();
    };
  }

  /* ------------------- option ------------------- */
  static function createOption(Request $request): array {
    return self::newOption($request);
  }
  static function changeOptions(Request $request): array {
    $optionsIds  = json_decode($request->input('optionsId'));
    $imagesId = $single = count($optionsIds) === 1;
    $fieldChange = json_decode($request->input('fieldChange'));

    $elementId = $request->input('elementId');
    if (empty($elementId)) return ['error' => 'Ошибка элемента!'];
    $name = $request->input('name');
    if ($single && OptionsElements::where([['name', $name], ['element_id', $elementId]])->count() > 1)
      return ['error' => 'Вариант с таким именем существует'];

    $moneyInputId  = $fieldChange->moneyInputId ? $request->input('moneyInputId') : false;
    $moneyOutputId = $fieldChange->moneyOutputId ? $request->input('moneyOutputId') : false;
    $unitId        = $fieldChange->unitId ? $request->input('unitId') : false;
    $property      = $fieldChange->property ? self::setProperty($request) : false;
    $activityChange = $fieldChange->activity;
    $activityChange && $activity = $request->input('activity') === 'true';
    $sort          = $fieldChange->sort ? $request->input('sort') : false;
    $inputPrice    = $single ? $request->input('inputPrice') : false;
    $percent       = $fieldChange->percent ? $request->input('percent') : false;
    $OutputPrice    = $single ? $request->input('OutputPrice') : false;

    if ($imagesId) {
      $imagesId = self::setFiles($request);

      $images = explode(',', $request->input('images'));
      foreach ($images as $id) {
        if (!empty($request->input('filesF_' . $id))) $imagesId .= ',' . $id;
      }

      $imagesId = trim($imagesId, ',');
    }

    $result['optionsIds'] = [];
    foreach ($optionsIds as $id) {
      $optionM = OptionsElements::find($id);

      $moneyInputId   && $optionM->money_input_id = $moneyInputId;
      $moneyOutputId  && $optionM->money_output_id = $moneyOutputId;
      $unitId         && $optionM->unit_id        = $unitId;
      $imagesId       && $optionM->images_id      = $imagesId;
      $name           && $optionM->name           = $name;
      $property       && $optionM->property       = $property;
      $activityChange && $optionM->activity       = $activity;
      $sort           && $optionM->sort           = $sort;
      $inputPrice     && $optionM->input_price    = $inputPrice;
      $percent        && $optionM->output_percent = $percent;
      $OutputPrice    && $optionM->output_price   = $OutputPrice;

      $optionM->save();
      $result['optionsIds'][] = $id;
    }

    return $result;
  }
  static function deleteOptions(Request $request) {
    $sectionId = $request->input('optionId');

    if ($sectionId) {
      $sectionM = Section::find($sectionId);
      $sectionM->delete();
    };
  }

  //---------------------------------------------------------------------------------------------
  // Entries
  //---------------------------------------------------------------------------------------------

  /**
   * @param string $tableName
   *
   * @return mixed
   */
  public function index() {
    // Загрузка столбцов из конфига
    $footerData = '';
    $setting = [];
    $elementsColumn = $setting['elementsColumn'] ?? 'id,codeName,name,activity,sort,lastEditDate';
    $footerData .= "<input type='hidden' id='elementsColumn' value='$elementsColumn'>";
    $optionsColumn = $setting['optionsColumn'] ?? 'id,images,name,unitId,activity,sort,lastEditDate,moneyInputId,inputPrice,moneyOutputId,outputPercent,outputPrice';
    $footerData .= "<input type='hidden' id='optionsColumn' value='$optionsColumn'>";

    $codes = Mdb::loadTable('codes', ['symbolCode', 'name']);
    $footerData .= "<input type='hidden' id='dataCodes' value='" . json_encode($codes) . "'>";
    $units = Mdb::loadTable('units', ['id', 'shortName', 'name']);
    $footerData .= "<input type='hidden' id='dataUnits' value='" . json_encode($units) . "'>";
    $money = Mdb::loadTable('money', ['id', 'shortName']);
    $footerData .= "<input type='hidden' id='dataMoney' value='" . json_encode($money) . "'>";

    // Все свойства
    $footerData .= "<input type='hidden' id='dataProperties' value='" . json_encode(self::loadProperties()) . "'>";

    // Переводы
    $load = new FileLoader(app()['files'], app()->langPath());
    $mess = $load->load(app()->getLocale(), 'db', '*');
    $footerData .= "<input type='hidden' id='dataDbLang' value='" . json_encode($mess) . "'>";

    return view('pages.catalog', [
      'footerData' => $footerData,
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
