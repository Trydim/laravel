<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
  use HasFactory;

  /**
   * Db tables
   *
   * @var string
   */
  protected $table = 'customers';

  /**
   * Следует ли обрабатывать временные метки модели.
   *
   * @var bool
   */
  public $timestamps = false;
}
