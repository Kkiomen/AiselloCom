<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Uruchamia migracje.
     * Tworzy tabelę api_usage_logs do logowania użycia API.
     */
    public function up(): void
    {
        Schema::create('api_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('api_key_id')->constrained()->onDelete('cascade');
            $table->string('endpoint')->comment('Endpoint API który został wywołany');
            $table->integer('tokens_used')->nullable()->comment('Liczba użytych tokenów');
            $table->decimal('cost', 10, 4)->nullable()->comment('Koszt requestu w USD');
            $table->integer('response_time_ms')->nullable()->comment('Czas odpowiedzi w milisekundach');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Wycofuje migracje.
     * Usuwa tabelę api_usage_logs.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_usage_logs');
    }
};
