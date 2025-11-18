<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_prompts', function (Blueprint $table) {
            $table->string('api_type')->default('product-description')->after('user_id');
            $table->index(['user_id', 'api_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_prompts', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'api_type']);
            $table->dropColumn('api_type');
        });
    }
};
