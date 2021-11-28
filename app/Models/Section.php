<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model {
  use HasFactory;

  /**
   * Следует ли обрабатывать временные метки модели.
   *
   * @var bool
   */
  public $timestamps = false;


}
