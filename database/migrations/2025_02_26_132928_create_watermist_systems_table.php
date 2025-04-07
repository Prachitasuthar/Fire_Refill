<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatermistSystemsTable extends Migration
{
    public function up()
    {
        Schema::create('watermist_systems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('provider_id')->constrained('users')->where('user_type', 'provider');
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('technology_type')->nullable();
            $table->string('nozzle_type')->nullable();
            $table->string('working_pressure')->nullable();
            $table->string('droplet_size')->nullable();
            $table->string('flow_rate')->nullable();
            $table->string('application_area')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('watermist_systems');
    }
}

