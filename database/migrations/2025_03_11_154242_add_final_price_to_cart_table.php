<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->decimal('final_price', 10, 2)->nullable()->after('price');
        });
    }
    
    public function down()
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->dropColumn('final_price');
        });
    }
    
};
