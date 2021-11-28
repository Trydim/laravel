<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoneyTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('money', function (Blueprint $table) {

      if (Schema::hasTable('money') && false) {
        // если таблица есть.
        Schema::table('money', function (Blueprint $table) {
        });

      } else {
        $table->charset = 'utf8mb4';
        $table->collation = 'utf8mb4_general_ci';

        $table->increments('id');
        $table->string('code', 10);
        $table->string('name');
        $table->string('short_name', 5)->nullable();
        $table->timestamp('last_edit_date')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        $table->decimal('rate', 8, 4)->default(1);
        $table->boolean('main')->nullable()->default(false);
      }

    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('money');
  }
}
