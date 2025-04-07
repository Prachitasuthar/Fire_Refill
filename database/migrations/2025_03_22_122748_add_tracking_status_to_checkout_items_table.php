<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::table('checkout_items', function (Blueprint $table) {
            $table->string('tracking_status')->default('confirmed')->after('final_price');
            $table->date('arrival_date')
                ->nullable()
                ->after('tracking_status')
                ->default(DB::raw('DATE_ADD(created_at, INTERVAL 2 DAY)'));
        });
    }

    public function down()
    {
        Schema::table('checkout_items', function (Blueprint $table) {
            $table->dropColumn('tracking_status');
            $table->dropColumn('arrival_date');
        });
    }
};

