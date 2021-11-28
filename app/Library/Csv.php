<?php

namespace App\Library;

class Csv {

  /**
   * @return string csv Path
   */
  static function csvPath(): string {
    return config('config.csv_path');
  }

  /**
   * @return int
   */
  static function csvLength(): int {
    return config('config.csv_string_length');
  }

  /**
   * @return string
   */
  static function csvSeparator(): string {
    return config('config.csv_separator');
  }

  /**
   * сделать поиск всех файлов, наверное. (хотя если их много переходить на БД, наверное)
   * @param string $path {string}
   * @param string $link {string}
   * @return mixed|null
   */
  static function scanDirCsv(string $path, string $link = '') {

    return array_reduce(scandir($path), function ($r, $item) use ($link) {
      if (!($item === '.' || $item === '..')) {
        if (stripos($item, '.csv')) {
          $r[] = [
            'fileName' => $item,
            'name'     => __('common.' . str_replace('.csv', '', $item)),
          ];
        } else {
          $link && $link .= '/';
          if (filetype(self::csvPath() . $link . $item) === 'dir') {
            $r[$item] = self::scanDirCsv(self::csvPath() . $link . $item, $link . $item);
          }
        }
      }

      return $r;
    }, []);
  }

  /**
   * @param $csvTable
   * @return array
   */
  static function openCsv($csvTable): array {
    $result = [];

    $filePath = Utilities::findingFile(self::csvPath(), $csvTable);

    if (($file = fopen($filePath, 'r'))) {
      $csvLength = static::csvLength();
      $csvSeparator = static::csvSeparator();

      while ($cells = fgetcsv($file, $csvLength, $csvSeparator)) {
        $cells = array_map(function ($cell) {
          if (!mb_detect_encoding($cell, 'UTF-8', true)) $cell = iconv('cp1251', 'UTF-8', $cell);
          return $cell;
        }, $cells);
        $result[] = $cells;
      }
    }

    return $result;
  }

  /*public function fileForceDownload() {
    $file = self::csvPath() . $this->csvTable;

    if (file_exists($file)) {
      // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
      // если этого не сделать файл будет читаться в память полностью!
      if (ob_get_level()) {
        ob_end_clean();
      }
      // заставляем браузер показать окно сохранения файла
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename=' . basename($file));
      header('Content-Transfer-Encoding: binary');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($file));
      // читаем файл и отправляем его пользователю
      readFile($file);
      exit;
    }
  }*/

  /**
   * @param string $csvTable
   * @param array $csvData
   */
  static function saveCsv(string $csvTable, array $csvData) {
    $filePath = Utilities::findingFile(self::csvPath(), $csvTable);

    if (file_exists($filePath)) {
      $fileStrings = [];
      $csvSeparator = self::csvSeparator();
      $length = count($csvData[0]);

      foreach ($csvData as $v) {
        $v[$length - 1] .= PHP_EOL;
        $fileStrings[] = implode($csvSeparator, $v);
      }

      file_put_contents($filePath, $fileStrings);
    }
    // file notfound
  }
}
