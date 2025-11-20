<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model użytkownika systemu.
 *
 * Reprezentuje użytkownika platformy Aisello API.
 * Użytkownik posiada klucze API, customowe prompty i historię wygenerowanych opisów.
 *
 * @property int $id
 * @property string $name
 * @property string|null $company_name
 * @property string $email
 * @property string $password
 * @property int $api_rate_limit
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atrybuty które mogą być masowo przypisywane.
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_name',
        'api_rate_limit',
        'is_active',
        'is_admin',
    ];

    /**
     * Atrybuty ukryte podczas serializacji.
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Castowanie atrybutów.
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
            'api_rate_limit' => 'integer',
        ];
    }

    /**
     * Relacja do kluczy API użytkownika.
     * Użytkownik może posiadać wiele kluczy API.
     *
     * @return HasMany<ApiKey>
     */
    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    /**
     * Relacja do aktywnych kluczy API użytkownika.
     * Zwraca tylko aktywne, niewygasłe klucze.
     *
     * @return HasMany<ApiKey>
     */
    public function activeApiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Relacja do customowych promptów użytkownika.
     * Użytkownik może mieć wiele szablonów promptów.
     *
     * @return HasMany<UserPrompt>
     */
    public function userPrompts(): HasMany
    {
        return $this->hasMany(UserPrompt::class);
    }

    /**
     * Relacja do domyślnego promptu użytkownika.
     * Zwraca domyślny szablon promptu dla użytkownika.
     *
     * @return HasOne<UserPrompt>
     */
    public function defaultPrompt(): HasOne
    {
        return $this->hasOne(UserPrompt::class)->where('is_default', true);
    }

    /**
     * Relacja do wygenerowanych opisów produktów.
     * Historia wszystkich wygenerowanych opisów przez użytkownika.
     *
     * @return HasMany<ProductDescription>
     */
    public function productDescriptions(): HasMany
    {
        return $this->hasMany(ProductDescription::class);
    }

    /**
     * Relacja do logów użycia API.
     * Historia wywołań API przez użytkownika.
     *
     * @return HasMany<ApiUsageLog>
     */
    public function apiUsageLogs(): HasMany
    {
        return $this->hasMany(ApiUsageLog::class);
    }
}
