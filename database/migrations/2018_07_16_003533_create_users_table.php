<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('noaccount');
            $table->string('fullname');
            $table->integer('nik')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone');
            $table->string('photo')->nullable();
            $table->string('token')->default(0);
            $table->integer('warning')->default(0);
            $table->integer('city_id');
            $table->enum('role', ['customer', 'dealer', 'sales', 'admin']);
            $table->string('challenge_code')->nullable();
            $table->boolean('is_confirm')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
