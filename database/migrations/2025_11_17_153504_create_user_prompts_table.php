<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Uruchamia migracje.
     * Tworzy tabelę user_prompts do przechowywania customowych promptów użytkowników.
     */
    public function up(): void
    {
        Schema::create('user_prompts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->comment('Nazwa szablonu promptu');
            $table->text('prompt_template')->comment('Szablon promptu do generowania opisów');
            $table->boolean('is_default')->default(false)->comment('Czy domyślny dla użytkownika');
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Wycofuje migracje.
     * Usuwa tabelę user_prompts.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_prompts');
    }
};
