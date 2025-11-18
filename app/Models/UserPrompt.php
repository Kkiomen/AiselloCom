<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model customowego promptu użytkownika.
 *
 * Reprezentuje szablon promptu do generowania opisów produktów.
 * Użytkownik może mieć wiele promptów, z czego jeden może być domyślny.
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $prompt_template
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 */
class UserPrompt extends Model
{
    /**
     * Atrybuty które mogą być masowo przypisywane.
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'api_type',
        'name',
        'prompt_template',
        'is_default',
    ];

    /**
     * Scope do filtrowania promptów po typie API.
     * Scope for filtering prompts by API type.
     *
     * @param \Illuminate\Database\Eloquent\Builder<UserPrompt> $query
     * @param string $apiType
     * @return void
     */
    public function scopeForApi(\Illuminate\Database\Eloquent\Builder $query, string $apiType): void
    {
        $query->where('api_type', $apiType);
    }

    /**
     * Castowanie atrybutów.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Relacja do użytkownika posiadającego prompt.
     * Każdy prompt należy do jednego użytkownika.
     *
     * @return BelongsTo<User, UserPrompt>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope do filtrowania tylko domyślnych promptów.
     * Zwraca tylko prompty które są oznaczone jako domyślne.
     *
     * @param Builder<UserPrompt> $query
     * @return void
     */
    public function scopeDefault(Builder $query): void
    {
        $query->where('is_default', true);
    }

    /**
     * Scope do filtrowania promptów danego użytkownika.
     *
     * @param Builder<UserPrompt> $query
     * @param int $userId
     * @return void
     */
    public function scopeByUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * Ustawia ten prompt jako domyślny dla użytkownika w ramach danego API.
     * Odznacza wszystkie inne prompty użytkownika dla tego API jako niedomyślne.
     *
     * @return bool
     */
    public function setAsDefault(): bool
    {
        // Najpierw usuń flagę default ze wszystkich promptów użytkownika dla tego API
        // First remove default flag from all user prompts for this API
        static::where('user_id', $this->user_id)
            ->where('api_type', $this->api_type)
            ->update(['is_default' => false]);

        // Ustaw ten prompt jako domyślny
        $this->is_default = true;
        return $this->save();
    }
}
