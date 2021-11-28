<?php

namespace App\Models;

use App\Models\Traits\All;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model {
  use HasFactory;

  use All;
  /**
   * Db tables
   *
   * @var string
   */
  //protected $table = 'my_flights';

  /**
   * Primary key name
   *
   * @var string
   */
  protected $primaryKey = 'symbol_code';

  /**
   * Primary key not autoincrement
   *
   * @var bool
   */
  public $incrementing = false;

  /**
   * Primary key data type
   *
   * @var string
   */
  protected $keyType = 'string';

  /**
   * Следует ли обрабатывать временные метки модели.
   *
   * @var bool
   */
  public $timestamps = false;

  /**
   * Значения по умолчанию для атрибутов модели.
   *
   * @var array
   */
  /*protected $attributes = [
    'delayed' => false,
  ];*/


}
