<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Uruchamia migracje.
     * Tworzy tabelę product_descriptions do przechowywania wygenerowanych opisów produktów.
     */
    public function up(): void
    {
        Schema::create('product_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('api_key_id')->constrained()->onDelete('cascade');
            $table->uuid('request_id')->unique()->comment('UUID dla tracingu requestu');
            $table->json('input_data')->comment('Oryginalne dane wejściowe od użytkownika');
            $table->json('enriched_data')->nullable()->comment('Dane po wzbogaceniu z web scraping');
            $table->text('generated_description')->nullable()->comment('Wygenerowany opis produktu');
            $table->text('prompt_used')->nullable()->comment('Użyty prompt do generowania');
            $table->integer('processing_time_ms')->nullable()->comment('Czas przetwarzania w milisekundach');
            $table->integer('tokens_used')->nullable()->comment('Liczba użytych tokenów');
            $table->decimal('cost', 10, 4)->nullable()->comment('Koszt generowania w USD');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable()->comment('Komunikat błędu w przypadku niepowodzenia');
            $table->timestamps();

            $table->index('request_id');
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Wycofuje migracje.
     * Usuwa tabelę product_descriptions.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_descriptions');
    }
};
