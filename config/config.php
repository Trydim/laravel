<?php

return [
  //'path_img' => . 'public/images/',

  /* Использовать БД (возможно стоит отделить в отдельную cms)  */
  //'use_database' => true,

  /* Возможность прямого редактирования БД из админки */
  'change_database' => false,

  /* Папка с csv файлами */
  'csv_develop'       => false,
  'csv_path'          => storage_path('app' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR),
  'csv_string_length' => env('CSV_STRING_LENGTH', 1000),
  'csv_separator'     => env('CSV_SEPARATOR', ';'),

  /**
   *
   */
  'setting_path' => storage_path('app' . DIRECTORY_SEPARATOR . 'settingSave.json'),

  /* Отображать таблицы в меню */
  //'DB_TABLE_IN_SIDEMENU' => true,

  /* Страница для доступа без регистрации: файл с таким именем должен быть в public/views/ */
  //'PUBLIC_PAGE' => 'calculator',

  /* Какую библиотеку использовать (добавить в настройки) mpdf, html2pdf */
  // внутри битрикс не доступно mpdf
  //'PDF_LIBRARY' => 'mpdf',

  /* Пункты меню какие показывать и последовательность */
  'access_menu' => ['calendar', 'catalog', 'orders', 'adminDb', 'customers', 'users', ],//  'fileManager', 'statistic'],
];
