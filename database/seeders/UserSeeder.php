<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ApiKey;
use App\Models\ApiUsageLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * User Seeder
 *
 * Seeder tworzący testowego użytkownika z kluczem API
 * Seeder creating test user with API key
 */
class UserSeeder extends Seeder
{
    /**
     * Uruchom seedowanie bazy danych
     * Run the database seeds.
     */
    public function run(): void
    {
        // Utwórz testowego użytkownika / Create test user
        $user = User::create([
            'name' => 'Test User',
            'company_name' => 'Aisello Test Company',
            'email' => 'admin@aisello.com',
            'password' => bcrypt('password'),
            'api_rate_limit' => 1000, // Wyższy limit dla testów / Higher limit for testing
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        echo "✅ Utworzono użytkownika testowego:\n";
        echo "   Email: admin@aisello.com\n";
        echo "   Hasło: password\n\n";

        // Wygeneruj klucz API / Generate API key
        $prefix = config('api.key.prefix', 'aic_');
        $length = config('api.key.length', 64);
        $rawKey = $prefix . Str::random($length - strlen($prefix));

        $apiKey = $user->apiKeys()->create([
            'name' => 'Test API Key',
            'key' => hash('sha256', $rawKey),
            'is_active' => true,
            'expires_at' => null,
            'last_used_at' => now()->subDays(1),
        ]);

        echo "✅ Wygenerowano klucz API testowy:\n";
        echo "   Nazwa: Test API Key\n";
        echo "   Klucz: {$rawKey}\n";
        echo "   ⚠️  Zapisz ten klucz - nie będzie dostępny później!\n\n";

        // Utwórz przykładowe logi użycia / Create sample usage logs
        $endpoints = [
            '/api/v1/products/generate-description',
        ];

        for ($i = 0; $i < 15; $i++) {
            ApiUsageLog::create([
                'user_id' => $user->id,
                'api_key_id' => $apiKey->id,
                'endpoint' => $endpoints[array_rand($endpoints)],
                'tokens_used' => rand(100, 1500),
                'cost' => rand(1, 50) / 10000, // Random cost between 0.0001 and 0.005
                'response_time_ms' => rand(200, 2000),
                'created_at' => now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
            ]);
        }

        echo "✅ Utworzono 15 przykładowych logów użycia API\n\n";

        // Utwórz drugi użytkownik (opcjonalnie) / Create second user (optional)
        $user2 = User::create([
            'name' => 'Jan Kowalski',
            'company_name' => 'Test Sp. z o.o.',
            'email' => 'jan@test.pl',
            'password' => bcrypt('password'),
            'api_rate_limit' => 100,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        echo "✅ Utworzono drugiego użytkownika testowego:\n";
        echo "   Email: jan@test.pl\n";
        echo "   Hasło: password\n\n";

        echo "==========================================\n";
        echo "🎉 Seedowanie zakończone pomyślnie!\n";
        echo "==========================================\n";
    }
}
