<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up() {
        Schema::create('cart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->nullable(); // Current logged-in user
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade')->nullable(); // Provider ID (Jo product sell kar raha hai)
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade')->nullable(); // Category Type
            $table->unsignedBigInteger('product_id')->nullable(); // Ye alag tables me se milega
            $table->integer('quantity')->default(1)->nullable();
            $table->decimal('price', 10, 2)->nullable(); // Product Price
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('cart');
    }
};

