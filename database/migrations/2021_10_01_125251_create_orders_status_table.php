<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersStatusTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('orders_status', function (Blueprint $table) {
      if (Schema::hasTable('orders_status') && false) {
        // если таблица есть.
        Schema::table('orders_status', function (Blueprint $table) {
        });

      } else {
        $table->charset = 'utf8mb4';
        $table->collation = 'utf8mb4_general_ci';

        $table->increments('id');
        $table->string('name', 50);
      }
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('orders_status');
  }
}
