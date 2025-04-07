<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupon_code')->unique()->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable(); 
            $table->unsignedBigInteger('product_id')->nullable(); 
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('discount')->nullable(); // Percentage
            $table->decimal('final_price', 10, 2)->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->integer('max_usage')->default(10)->nullable(); 
            $table->integer('used_count')->default(0)->nullable(); // Used count
            $table->enum('status', ['active', 'expired'])->default('active')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('provider_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('coupons');
    }
};
