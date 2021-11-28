<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateElementsTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('elements', function (Blueprint $table) {

      if (Schema::hasTable('elements') && false) {
        // если таблица есть.
        Schema::table('elements', function (Blueprint $table) {


        });

      } else {
        $table->charset = 'utf8mb4';
        $table->collation = 'utf8mb4_general_ci';

        $table->increments('id');
        $table->string('element_type_code')->nullable()->default('null');
        $table->unsignedInteger('section_parent_id');
        $table->string('name');
        $table->timestamp('last_edit_date')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));;
        $table->boolean('activity')->default(true);
        $table->unsignedInteger('sort')->default(100);

        $table->index('element_type_code');
        $table->index('section_parent_id');

        $table->foreign('element_type_code')->references('symbol_code')->on('codes');
        $table->foreign('section_parent_id')->references('id')->on('sections');
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
  }
}
