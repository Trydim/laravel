<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitsTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('units', function (Blueprint $table) {

      if (Schema::hasTable('units') && false) {
        // если таблица есть.
        Schema::table('units', function (Blueprint $table) {
        });

      } else {
        $table->charset = 'utf8mb4';
        $table->collation = 'utf8mb4_general_ci';

        $table->increments('id');
        $table->string('name');
        $table->string('short_name', 10);
        $table->boolean('activity')->default(true);
      }

    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('units');
  }
}
