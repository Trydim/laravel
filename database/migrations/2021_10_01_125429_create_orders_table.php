<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('orders', function (Blueprint $table) {
      if (Schema::hasTable('orders') && false) {
        // если таблица есть.
        Schema::table('orders', function (Blueprint $table) {
        });

      } else {
        $table->charset = 'utf8mb4';
        $table->collation = 'utf8mb4_general_ci';

        $table->increments('id');
        $table->timestamp('create_date')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamp('last_edit_date')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        $table->unsignedBigInteger('user_id');
        $table->unsignedInteger('customer_id');
        $table->decimal('total');
        $table->string('important_value')->default('');
        $table->unsignedInteger('status_id');
        $table->string('save_value', 500);
        $table->binary('report_value');


        $table->index('user_id');
        $table->index('customer_id');
        $table->index('status_id');

        $table->foreign('user_id')->references('id')->on('users');
        $table->foreign('customer_id')->references('id')->on('customers');
        $table->foreign('status_id')->references('id')->on('orders_status');
      }
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('orders');
  }
}
