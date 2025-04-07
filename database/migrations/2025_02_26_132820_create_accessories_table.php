<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessoriesTable extends Migration
{
    public function up()
    {
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('provider_id')->constrained('users')->where('user_type', 'provider');
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->float('weight')->nullable();
            $table->string('power_source')->nullable();
            $table->string('operating_voltage')->nullable();
            $table->string('material')->nullable();
            $table->string('working_temprature')->nullable();
            $table->string('IP_routing')->nullable();
            $table->integer('stock')->nullable();
            $table->string('warranty')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accessories');
    }
}
