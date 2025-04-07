<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::table('accessories', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id')->nullable()->after('id');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('accessories', function (Blueprint $table) {
            // Check if the foreign key exists before dropping
            $foreignKeys = DB::select("SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = 'accessories' AND COLUMN_NAME = 'coupon_id'");
    
            if (!empty($foreignKeys)) {
                $table->dropForeign(['coupon_id']);
            }
    
            // Drop the column only if it exists
            if (Schema::hasColumn('accessories', 'coupon_id')) {
                $table->dropColumn('coupon_id');
            }
        });
    }
    
};
