<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model klucza API.
 *
 * Reprezentuje klucz API użytkownika używany do autentykacji requestów.
 * Klucze są hashowane przed zapisem i mogą mieć datę wygaśnięcia.
 *
 * @property int $id
 * @property int $user_id
 * @property string $key
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 */
class ApiKey extends Model
{
    /**
     * Atrybuty które mogą być masowo przypisywane.
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'key',
        'name',
        'last_used_at',
        'expires_at',
        'is_active',
    ];

    /**
     * Atrybuty ukryte podczas serializacji.
     * Klucz API nie powinien być zwracany w odpowiedziach.
     *
     * @var array<string>
     */
    protected $hidden = [
        'key',
    ];

    /**
     * Castowanie atrybutów.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relacja do użytkownika posiadającego klucz.
     * Każdy klucz należy do jednego użytkownika.
     *
     * @return BelongsTo<User, ApiKey>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacja do opisów produktów wygenerowanych tym kluczem.
     *
     * @return HasMany<ProductDescription>
     */
    public function productDescriptions(): HasMany
    {
        return $this->hasMany(ProductDescription::class);
    }

    /**
     * Relacja do logów użycia API dla tego klucza.
     *
     * @return HasMany<ApiUsageLog>
     */
    public function apiUsageLogs(): HasMany
    {
        return $this->hasMany(ApiUsageLog::class);
    }

    /**
     * Scope do filtrowania tylko aktywnych kluczy.
     * Zwraca tylko klucze które są aktywne (is_active = true).
     *
     * @param Builder<ApiKey> $query
     * @return void
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope do filtrowania wygasłych kluczy.
     * Zwraca tylko klucze które już wygasły.
     *
     * @param Builder<ApiKey> $query
     * @return void
     */
    public function scopeExpired(Builder $query): void
    {
        $query->where('expires_at', '<=', now())
            ->whereNotNull('expires_at');
    }

    /**
     * Sprawdza czy klucz jest ważny.
     * Klucz jest ważny jeśli jest aktywny i nie wygasł.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        // Sprawdź czy klucz jest aktywny
        if (!$this->is_active) {
            return false;
        }

        // Sprawdź czy klucz nie wygasł
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Oznacza klucz jako użyty.
     * Aktualizuje pole last_used_at na obecny czas.
     *
     * @return bool
     */
    public function markAsUsed(): bool
    {
        $this->last_used_at = now();
        return $this->save();
    }

    /**
     * Deaktywuje klucz API.
     * Ustawia is_active na false.
     *
     * @return bool
     */
    public function revoke(): bool
    {
        $this->is_active = false;
        return $this->save();
    }
}
