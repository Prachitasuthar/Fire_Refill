<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->boolean('status')->default(0)->after('contact')->nullable(); // 0 = Pending, 1 = Accepted
        });
    }

    public function down(): void {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
