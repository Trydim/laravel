<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('sections', function (Blueprint $table) {

      if (Schema::hasTable('sections') && false) {
        // если таблица есть.
        Schema::table('section', function (Blueprint $table) {

        });

      } else {
        $table->charset = 'utf8mb4';
        $table->collation = 'utf8mb4_general_ci';

        $table->increments('id');
        $table->unsignedInteger('parent_id')->default(0);
        $table->string('code');
        $table->string('name');
        $table->boolean('activity')->default(true);

        $table->index('parent_id');
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
    Schema::dropIfExists('sections');
  }
}
