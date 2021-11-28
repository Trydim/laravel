<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodesTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('codes', function (Blueprint $table) {

      if (Schema::hasTable('codes') && false) {
        // если таблица есть.
        Schema::table('codes', function (Blueprint $table) {
          //$table->integer('votes');
        });
      }
      else {
        $table->charset = 'utf8mb4';
        $table->collation = 'utf8mb4_general_ci';

        $table->string('symbol_code');
        $table->string('name');

        $table->primary('symbol_code');
      }
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('elements');
    Schema::dropIfExists('codes');
  }
}
