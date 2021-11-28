<?php

namespace App\Library\Traits;


use Illuminate\Translation\FileLoader;

/**
 * Trait dictionary
 */
trait Dictionary {

  /**
   * @return array
   */
  private static function getDictionary(): array {
    $load = new FileLoader(app()['files'], app()->langPath());
    return $load->load(app()->getLocale(), 'common', '*');
  }

  private static function includeFromSetting() {
    /*if (isset(self->setting['managerSetting'])) {
      $list = self->setting['managerSetting'];
      return array_reduce(array_keys($list), function ($r, $k) use ($list) {
        $r[$k] = $list[$k]['name'];
        return $r;
      }, []);
    }
    return [];*/
  }

  static function initDictionary(): string {
    $mess = json_encode(self::getDictionary());
    return $mess ? "<input type='hidden' id='dictionaryData' value='$mess'>" : '';
  }
}
