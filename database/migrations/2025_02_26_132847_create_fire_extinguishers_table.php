<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFireExtinguishersTable extends Migration
{
    public function up()
    {
        Schema::create('fire_extinguishers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('provider_id')->constrained('users')->where('user_type', 'provider');
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('fire_class')->nullable();
            $table->string('suitability')->nullable();
            $table->float('capacity')->nullable();
            $table->string('extinguishing_agent')->nullable();
            $table->string('discharge_time')->nullable();
            $table->string('working_pressure')->nullable();
            $table->string('cylinder_material')->nullable();
            $table->string('operating_temprature')->nullable();
            $table->float('weight')->nullable();
            $table->integer('stock')->nullable();
            $table->string('warranty')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fire_extinguishers');
    }
}

