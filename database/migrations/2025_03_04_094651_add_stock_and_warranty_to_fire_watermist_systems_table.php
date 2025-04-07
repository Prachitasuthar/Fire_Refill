<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('watermist_systems', function (Blueprint $table) {
            $table->integer('stock')->after('application_area')->default(0)->nullable;
            $table->string('warranty')->after('stock')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('watermist_systems', function (Blueprint $table) {
            $table->dropColumn(['stock', 'warranty']);
        });
    }
};
