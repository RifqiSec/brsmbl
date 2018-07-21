<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vehicle_type_id');
            $table->integer('vehicle_brand_id');
            $table->string('name');
            $table->decimal('harga');
            $table->enum('fuel', ['Bensin', 'Solar', 'Diesel']);
            $table->string('fuel_consumption');
            $table->string('engine');
            $table->string('hp');
            $table->string('torque');
            $table->string('transmition');
            $table->string('gear_box');
            $table->enum('wd_type', ['Roda depan', 'Roda Belakang', '4WD']);
            $table->string('cylinder');
            $table->string('seat');
            $table->enum('door', [2, 3, 4, 5, 6, 7, 8]);
            $table->string('dimension');
            $table->string('fuel_tank');
            $table->string('velg');
            $table->boolean('front_brake');
            $table->boolean('rear_brake');
            $table->string('net_weight');
            $table->string('photo');
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
        Schema::dropIfExists('vehicle');
    }
}
