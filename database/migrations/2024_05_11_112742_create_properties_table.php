<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name',191)->nullable();
            $table->string('address',191)->nullable();
            $table->string('city',191)->nullable();
            $table->text('description')->nullable();
            $table->double('price',10,2)->nullable();
            $table->string('property_type',191)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('parking_two_wheel')->default(0);
            $table->tinyInteger('parking_for_wheel')->default(0);
            $table->tinyInteger('electricity')->default(0);
            $table->tinyInteger('furniture')->default(0);
            $table->tinyInteger('other_electric_accessories')->default(0);
            $table->string('client_name',191)->nullable();
            $table->string('client_phone',191)->nullable();
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
        Schema::dropIfExists('properties');
    }
}
