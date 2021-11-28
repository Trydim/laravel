<?php

namespace App\Library;

use App\Library\Traits\Dictionary;
use App\Library\Traits\Hooks;
use App\Library\Traits\Setting;

class Utilities {
  use Dictionary;
  use Hooks;
  use Setting;

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
   * @param     $var
   * @param int $die
   */
  static function de($var, $die = 1) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    if ($die) die();
  }

  /**
   * Find index of Levelshtein
   *
   * @param string - when search?
   * @param array - what search? arr of string
   * @param string - in charset in csv (autodetect)
   * @param bool - if true return word, default return index position
   *
   * @return integer or string - int: return index of position keyword in array
   */
  static function findWord($input, $cell, $inCharset = 'windows-1251', $index = false) {
    $input = mb_strtolower($input, 'UTF-8');
    $shortest = -1;
    $gc = false;
    $nearest_word = null;
    $limit = static::getLimitLevenshtein($input); // Порог прохождения
    foreach ($cell as $key => $item) {
      $word = trim(mb_strtolower(iconv($inCharset, 'UTF-8', $item), 'UTF-8'));
      $lev = levenshtein($input, $word);
      if ($lev === 0) {
        $gc = $key;
        $nearest_word = $word;
        break;
      }
      if ($lev < $limit && ($lev <= $shortest || $shortest < 0)) {
        $gc = $key;
        $shortest = $lev;
        $nearest_word = $word;
      }
    }
    if ($index) {
      return $nearest_word;
    }

    return $gc;
  }

  /**
   * Find key
   *
   * @param string - when search?
   * @param array - what search? array of keys
   *
   * @return string - keys or false
   */
  static function findKey($cell, $input) {
    $count = count($input); // теперь всегда 1
    $input = '/(' . implode('|', $input) . ')/i';
    foreach ($cell as $key => $item) {
      if (preg_match_all($input, $key) === $count) {
        return $key;
      }
    }

    return false;
  }

  /**
   * Load csv to array$_FILES['pictureHead']['error']
   *
   * @param array - dictionary for search on the key. example: ['name' => 'Имя'].
   * @param string - csv filename with path
   * @param bool - if true that return one rang array
   *
   * @return mixed array or bool
   */
  static function loadCVS($dict, $filename, $one_rang = false) {
    $csvPath = self::csvPath();
    $csvSeparator = self::csvSeparator();
    $csvLength = self::csvLength();

    $filename = $csvPath . $filename;
    $result = [];

    if (!count($dict)) return static::loadFullCVS($filename);

    if (file_exists($filename) && ($handle = fopen($filename, "r")) !== false) {
      if (($data = fgetcsv($handle, $csvLength, $csvSeparator))) {
        $keyIndex = [];

        $inCharset = 'UTF-8';

        foreach ($dict as $key => $word) {
          $bool = is_array($word);
          $keyWord = $bool ? $word[0] : $word;
          $i = static::findWord($keyWord, $data, $inCharset);
          if ($i !== false) {
            if ($bool) $keyIndex[$key] = [$i, $word[1]];
            else $keyIndex[$key] = $i;
          }
        }
        if ($one_rang) {

          foreach ($keyIndex as $item) {
            $addpos = function ($data) use ($item) { return $data[$item]; };
          }

        } else {

          $addpos = function ($data) use ($keyIndex, $inCharset) {
            $arr = [];
            foreach ($keyIndex as $key => $item) {
              if (is_array($item)) {
                $arr[$key] = trim(iconv($inCharset, 'UTF-8', $data[$item[0]]));
                $arr[$key] = convert($item[1], $arr[$key]);
              } else $arr[$key] = trim(iconv($inCharset, 'UTF-8', $data[$item]));
            }
            return $arr;
          };

        }

        while (($data = fgetcsv($handle, $csvLength, $csvSeparator)) !== false) {
          $result[] = $addpos($data);
        }
      }
      fclose($handle);
    } else {
      return false; //файла нет
    }

    return $result;
  }

  /**
   * Поиск в первых пяти строках начала таблиц
   *
   * @param $path
   * @return array|bool
   */
  static function loadFullCVS($path) {
    $csvLength = static::csvLength();
    $csvSeparator = static::csvSeparator();

    if (file_exists($path) && ($handle = fopen($path, "r")) !== false) {
      $result = [];
      $emptyRow = 0;
      while ($emptyRow < 5) { // Пять пустрых строк характеристик считаем что больше нету
        if (($data = fgetcsv($handle, $csvLength, $csvSeparator))) {
          if (!mb_strlen(implode('', $data))) {
            $emptyRow++;
            continue;
          }
          if ($emptyRow > 0) $emptyRow = 0;

          $result[] = $data;
        } else $emptyRow++;
      }
      fclose($handle);
    } else return false;

    return $result;
  }

  /**
   * @param $word
   *
   * @return false|float|int
   */
  static function getLimitLevenshtein($word) {
    if (iconv_strlen($word) <= 3) {
      return iconv_strlen($word);
    }

    return ceil(iconv_strlen($word) / 2);
  }

  static function convert($type, $value) {
    switch ($type) {
      case 'int':
      case 'integer':
        return floor((integer)$value);
      case 'float':
      case 'double':
        return floatval(str_replace(',', '.', $value));
    }
    return $value;
  }

  static function convertToArray($value) {
    if (is_array($value)) return $value;
    if (is_string($value)) {
      return array_map(function ($item) { return trim($item); }, explode(',', $value));
    }
  }

  /*static function getPageAsString($data, $wrapId = 'wrapCalcNode') {
    $html = "<div class=\"shadow-calc\" id=\"shadow-calc\"><shadow-calc></shadow-calc></div>";
    $html .= "<div id=\"$wrapId\" style='display:none;'>" . $data['cssLinksArr'];
    $html .= $data['globalWindowJsValue'];
    $html .= $data['content'];
    $html .= $data['jsLinksArr'];
    $html .= $data['footerContent'] . '</div>';

    return $html;
  }*/

  /**
   * @param $value {string}
   * @return string
   */
  static function translit($value) {
    $converter = [
      'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
      'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
      'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
      'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
      'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
      'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
      'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
    ];

    return strtr(mb_strtolower($value), $converter);
  }

  /**
   * @param $dir {string} - path without slash on the end
   * @param $fileName {string} - only file name without slash
   * @return false|string
   */
  static function findingFile($absolutePath, $fileName) {

    if (file_exists($absolutePath . $fileName)) return $absolutePath . $fileName;

    $arrDir = array_values(array_filter(scandir($absolutePath), function ($dir) use ($absolutePath) {
      return !($dir === '.' || $dir === '..' || is_file($absolutePath . $dir));
    }));

    $length = count($arrDir);
    for ($i = 0; $i < $length; $i++) {
      $result = static::findingFile($absolutePath . $arrDir[$i] . DIRECTORY_SEPARATOR, $fileName);
      if ($result) return $result;
    }

    return false;
  }
}
