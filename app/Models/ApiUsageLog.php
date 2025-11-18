<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model logu użycia API.
 *
 * Reprezentuje pojedyncze wywołanie API i służy do monitorowania
 * użycia, kosztów i wydajności.
 *
 * @property int $id
 * @property int $user_id
 * @property int $api_key_id
 * @property string $endpoint
 * @property int|null $tokens_used
 * @property float|null $cost
 * @property int|null $response_time_ms
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read User $user
 * @property-read ApiKey $apiKey
 */
class ApiUsageLog extends Model
{
    /**
     * Wyłączenie automatycznego zarządzania updated_at.
     * Ta tabela używa tylko created_at.
     *
     * @var bool
     */
    public const UPDATED_AT = null;

    /**
     * Atrybuty które mogą być masowo przypisywane.
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'api_key_id',
        'product_description_id',
        'endpoint',
        'tokens_used',
        'cost',
        'response_time_ms',
    ];

    /**
     * Castowanie atrybutów.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tokens_used' => 'integer',
            'cost' => 'decimal:4',
            'response_time_ms' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relacja do użytkownika.
     * Każdy log należy do jednego użytkownika.
     *
     * @return BelongsTo<User, ApiUsageLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacja do klucza API.
     * Każdy log należy do jednego klucza API.
     *
     * @return BelongsTo<ApiKey, ApiUsageLog>
     */
    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    /**
     * Relacja do opisu produktu.
     * Log może być powiązany z wygenerowanym opisem produktu.
     *
     * @return BelongsTo<ProductDescription, ApiUsageLog>
     */
    public function productDescription(): BelongsTo
    {
        return $this->belongsTo(ProductDescription::class);
    }
}
