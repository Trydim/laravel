<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientOrdersTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('client_orders', function (Blueprint $table) {
      if (Schema::hasTable('client_orders') && false) {
        // если таблица есть.
        Schema::table('client_orders', function (Blueprint $table) {
        });

      } else {
        $table->charset = 'utf8mb4';
        $table->collation = 'utf8mb4_general_ci';

        $table->increments('id');
        $table->string('cp_number', 30)->default('');
        $table->timestamp('create_date');
        $table->string('input_value', 500);
        $table->string('important_value', 500);
        $table->decimal('total');
      }
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('client_orders');
  }
}
