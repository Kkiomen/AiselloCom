<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Dodaje pole is_admin do tabeli users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email');
        });

        // Dodaj kolumnę serper_cost do api_usage_logs dla śledzenia kosztów Serper API
        if (Schema::hasTable('api_usage_logs') && !Schema::hasColumn('api_usage_logs', 'serper_cost')) {
            Schema::table('api_usage_logs', function (Blueprint $table) {
                $table->decimal('serper_cost', 10, 6)->nullable()->after('cost');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });

        if (Schema::hasTable('api_usage_logs') && Schema::hasColumn('api_usage_logs', 'serper_cost')) {
            Schema::table('api_usage_logs', function (Blueprint $table) {
                $table->dropColumn('serper_cost');
            });
        }
    }
};
