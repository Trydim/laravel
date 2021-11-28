<?php

namespace App\Library\Traits;

/**
 * Trait SettingFile
 */
trait Setting {

  /**
   * @return string
   */
  private static function getSettingPath(): string {
    return config('config.setting_path');
  }

  /**
   * @param bool $decode
   * @param bool $assoc
   * @return array|false|mixed|string
   */
  static function getSettingFile(bool $decode = true, bool $assoc = true) {
    $settingPath = self::getSettingPath();

    if (file_exists($settingPath)) {
      $setting = file_get_contents($settingPath);
      return $decode ? json_decode($setting, $assoc) : $setting;
    }
    return $decode ? [] : '[]';
  }

  /**
   * @param array $content
   */
  static function setSettingFile(array $content) {
    file_put_contents(self::getSettingPath(), json_encode($content));
  }
}
