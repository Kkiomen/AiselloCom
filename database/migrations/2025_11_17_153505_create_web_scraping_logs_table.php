<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Uruchamia migracje.
     * Tworzy tabelę web_scraping_logs do logowania operacji web scrapingu.
     */
    public function up(): void
    {
        Schema::create('web_scraping_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_description_id')->constrained()->onDelete('cascade');
            $table->string('search_query')->comment('Query użyte do wyszukiwania');
            $table->string('url_scraped')->comment('URL ze scrapowanych danych');
            $table->json('data_extracted')->nullable()->comment('Wyekstrahowane dane z HTML');
            $table->boolean('success')->default(true)->comment('Czy scraping się powiódł');
            $table->timestamps();

            $table->index('product_description_id');
        });
    }

    /**
     * Wycofuje migracje.
     * Usuwa tabelę web_scraping_logs.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_scraping_logs');
    }
};
