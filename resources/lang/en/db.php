<?php

return [

  'codes' => [
    'table'      => 'Коды',
    'symbolCode' => 'Код',
    'name'       => 'Имя',
  ],

  'customers' => [ // Перевод Клиенты
    'id'       => 'Номер',
    'name'     => 'Имя',
    'itn'      => 'ИНН',
    'email'    => 'Почта',
    'phone'    => 'Номер',
    'contacts' => 'Контакты',
    'orders'   => 'Заказы',
  ],

  'orders' => [ // Перевод шапки заказов
    'id'            => 'Номер',
    'createDate'    => 'Дата',
    'lastEditDate'  => 'Дата Редактирования',
    'userName'      => 'Менеджер',
    'customerName'  => 'Клиент',
    'total'           => 'Сумма',
    'important_value' => 'Подробности',
    'status'          => 'Статус',
  ],

  'visitorOrders' => [ // Перевод шапки заказов
    'id'             => 'Номер',
    'cpNumber'       => 'Номер КП',
    'createDate'     => 'Дата создания',
    'total'          => 'Сумма',
    'importantValue' => 'Подробности',
  ],

  'users' => [
    'id'             => 'Номер',
    'permissionName' => 'Права', // Название прав доступа
    'login'          => 'Логин',
    'name'           => 'Имя',
    'email'          => 'Почта',
    'phone'          => 'Телефон',
    'registerDate'   => 'Дата',
    'activity'       => 'Активен',
  ],

  'elements' => [ // Перевод Элементов
    'id'           => 'Номер',
    'code'         => 'Тип',
    'name'         => 'Название',
    'activity'     => 'Активен',
    'sort'         => 'Сортировка',
    'lastEditDate' => 'Последнее изменение',
  ],

  'options' => [ // Перевод Вариантов
    'id'              => 'Номер',
    'images'          => 'Изображения',
    'name'            => 'Название',
    'unitName'        => 'Eд. измерения',
    'activity'        => 'Активен',
    'sort'            => 'Сортировка',
    'lastEditDate'    => 'Последнее изменение',
    'moneyInputName'  => 'Валюта вход',
    'inputPrice'      => 'Цена вход',
    'moneyOutputName' => 'Валюта выход',
    'outputPercent'   => 'Наценка',
    'outputPrice'     => 'Цена выход',
  ],

  'property' => [ // Свойства Вариантов
    'name' => 'Название',
    'code' => 'Код',
    'type' => 'Тип',
  ],
];
