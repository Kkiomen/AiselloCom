<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Uruchamia migracje.
     * Tworzy tabelę api_keys do przechowywania kluczy API użytkowników.
     */
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('key', 64)->unique();
            $table->string('name')->comment('Nazwa klucza dla użytkownika');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('key');
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Wycofuje migracje.
     * Usuwa tabelę api_keys.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
