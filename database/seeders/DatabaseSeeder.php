<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed settings, categories, tags, articles, and navigation
        $this->call([
            SettingsSeeder::class,
            CategorySeeder::class,
            NavigationSeeder::class, // After categories, as it depends on them
            TagSeeder::class,
            ArticleSeeder::class,
        ]);
    }
}
