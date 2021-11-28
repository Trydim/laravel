<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('users', function (Blueprint $table) {
      if (Schema::hasTable('users') && false) {
        // если таблица есть.
        Schema::table('users', function (Blueprint $table) {
        });

      } else {
        $table->charset = 'utf8mb4';
        $table->collation = 'utf8mb4_general_ci';

        $table->id();
        $table->unsignedInteger('permission_id')->default(1);
        $table->string('login')->unique();
        $table->string('password');
        $table->string('name');
        $table->string('email')->unique();
        $table->string('phone')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->boolean('activity')->default(1);
        $table->string('customization', 1000)->default('');
        $table->rememberToken();
        $table->timestamps();

        $table->index('permission_id');

        $table->foreign('permission_id')->references('id')->on('permission');
      }
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('users');
  }
}
