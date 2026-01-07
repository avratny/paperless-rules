<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Populate settings from .env if they don't exist yet
        $settings = [
            'paperless.url' => env('PAPERLESS_URL', 'http://localhost:8010'),
            'paperless.api_key' => env('PAPERLESS_API_KEY', ''),
            'ollama.url' => env('OLLAMA_URL', 'http://localhost:11434'),
            'ollama.model' => env('OLLAMA_MODEL', 'llama3.1'),
        ];

        foreach ($settings as $key => $value) {
            // Only create if not exists
            if (!Setting::has($key)) {
                Setting::set($key, $value);
            }
        }
    }
}

