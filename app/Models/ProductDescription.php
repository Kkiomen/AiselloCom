<?php

namespace App\Models;

use App\Enums\ProductDescriptionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model opisu produktu.
 *
 * Reprezentuje wygenerowany opis produktu wraz z wszystkimi metadanymi
 * procesu generowania (czas, koszt, tokeny, status, błędy).
 *
 * @property int $id
 * @property int $user_id
 * @property int $api_key_id
 * @property string $request_id
 * @property array $input_data
 * @property array|null $enriched_data
 * @property string|null $generated_description
 * @property string|null $prompt_used
 * @property int|null $processing_time_ms
 * @property int|null $tokens_used
 * @property float|null $cost
 * @property ProductDescriptionStatus $status
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @property-read ApiKey $apiKey
 */
class ProductDescription extends Model
{
    /**
     * Atrybuty które mogą być masowo przypisywane.
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'api_key_id',
        'request_id',
        'input_data',
        'enriched_data',
        'generated_description',
        'prompt_used',
        'processing_time_ms',
        'tokens_used',
        'cost',
        'status',
        'error_message',
    ];

    /**
     * Castowanie atrybutów.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'input_data' => 'array',
            'enriched_data' => 'array',
            'status' => ProductDescriptionStatus::class,
            'processing_time_ms' => 'integer',
            'tokens_used' => 'integer',
            'cost' => 'decimal:4',
        ];
    }

    /**
     * Relacja do użytkownika który stworzył opis.
     * Każdy opis należy do jednego użytkownika.
     *
     * @return BelongsTo<User, ProductDescription>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacja do klucza API użytego do wygenerowania opisu.
     * Każdy opis został wygenerowany przy użyciu konkretnego klucza API.
     *
     * @return BelongsTo<ApiKey, ProductDescription>
     */
    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    /**
     * Relacja do logów web scrapingu dla tego opisu.
     * Opis może mieć wiele logów scrapingu (różne URLs).
     *
     * @return HasMany<WebScrapingLog>
     */
    public function webScrapingLogs(): HasMany
    {
        return $this->hasMany(WebScrapingLog::class);
    }

    /**
     * Scope do filtrowania tylko zakończonych opisów.
     * Zwraca tylko opisy ze statusem COMPLETED.
     *
     * @param Builder<ProductDescription> $query
     * @return void
     */
    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', ProductDescriptionStatus::COMPLETED);
    }

    /**
     * Scope do filtrowania tylko nieudanych opisów.
     * Zwraca tylko opisy ze statusem FAILED.
     *
     * @param Builder<ProductDescription> $query
     * @return void
     */
    public function scopeFailed(Builder $query): void
    {
        $query->where('status', ProductDescriptionStatus::FAILED);
    }

    /**
     * Scope do filtrowania opisów danego użytkownika.
     *
     * @param Builder<ProductDescription> $query
     * @param int $userId
     * @return void
     */
    public function scopeByUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * Scope do filtrowania według statusu.
     *
     * @param Builder<ProductDescription> $query
     * @param ProductDescriptionStatus $status
     * @return void
     */
    public function scopeByStatus(Builder $query, ProductDescriptionStatus $status): void
    {
        $query->where('status', $status);
    }

    /**
     * Oznacza opis jako przetwarzany.
     * Zmienia status na PROCESSING.
     *
     * @return bool
     */
    public function markAsProcessing(): bool
    {
        $this->status = ProductDescriptionStatus::PROCESSING;
        return $this->save();
    }

    /**
     * Oznacza opis jako zakończony pomyślnie.
     * Zmienia status na COMPLETED.
     *
     * @return bool
     */
    public function markAsCompleted(): bool
    {
        $this->status = ProductDescriptionStatus::COMPLETED;
        return $this->save();
    }

    /**
     * Oznacza opis jako nieudany.
     * Zmienia status na FAILED i zapisuje komunikat błędu.
     *
     * @param string $errorMessage
     * @return bool
     */
    public function markAsFailed(string $errorMessage): bool
    {
        $this->status = ProductDescriptionStatus::FAILED;
        $this->error_message = $errorMessage;
        return $this->save();
    }
}
