<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFireSuppressionSystemsTable extends Migration
{
    public function up()
    {
        Schema::create('fire_suppression_systems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('provider_id')->constrained('users')->where('user_type', 'provider');
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('suppression_type')->nullable();
            $table->string('installation_type')->nullable();
            $table->string('application_area')->nullable();
            $table->float('cylinder_capacity')->nullable();
            $table->string('activation_method')->nullable();
            $table->string('response_time')->nullable();
            $table->string('working_temprature_range')->nullable();
            $table->integer('stock')->nullable();
            $table->string('warranty')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fire_suppression_systems');
    }
}
