<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOptionsElementsTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('options_elements', function (Blueprint $table) {

      if (Schema::hasTable('options_elements') && false) {
        // если таблица есть.
        Schema::table('options_elements', function (Blueprint $table) {
        });

      } else {
        $table->charset = 'utf8mb4';
        $table->collation = 'utf8mb4_general_ci';

        $table->increments('id');
        $table->unsignedInteger('element_id');
        $table->unsignedInteger('money_input_id')->default(1);
        $table->unsignedInteger('money_output_id')->default(1);
        $table->unsignedInteger('unit_id')->default(1);
        $table->string('images_id')->nullable();
        $table->string('name');
        $table->string('property', 1000)->nullable();
        $table->timestamp('last_edit_date')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        $table->boolean('activity')->default(true);
        $table->unsignedInteger('sort')->default(100);
        $table->decimal('input_price', 8, 4)->default(1);
        $table->double('output_percent')->default(0);
        $table->decimal('output_price', 8, 4)->default(1);

        $table->index('element_id');
        $table->index('money_input_id');
        $table->index('money_output_id');
        $table->index('unit_id');

        $table->foreign('element_id')->references('id')->on('elements')->onDelete('cascade');
        $table->foreign('money_input_id')->references('id')->on('money');
        $table->foreign('money_output_id')->references('id')->on('money');
        $table->foreign('unit_id')->references('id')->on('units');
      }

    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('options_elements');
  }
}
