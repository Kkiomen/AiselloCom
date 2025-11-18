<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Database Seeder
 *
 * Główny seeder aplikacji
 * Main application seeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Uruchom seedowanie bazy danych
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
    }
}
