<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class EncryptSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:encrypt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt sensitive settings in the database';

    /**
     * List of keys that should be encrypted
     */
    private array $encryptedKeys = [
        'paperless.api_key',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Encrypting sensitive settings...');

        foreach ($this->encryptedKeys as $key) {
            $setting = Setting::where('key', $key)->first();

            if (!$setting) {
                $this->warn("Setting '{$key}' not found, skipping...");
                continue;
            }

            // Check if already encrypted by trying to decrypt
            try {
                Crypt::decryptString($setting->value);
                $this->info("Setting '{$key}' is already encrypted, skipping...");
                continue;
            } catch (\Exception $e) {
                // Not encrypted, proceed with encryption
            }

            // Encrypt the value
            $encryptedValue = Crypt::encryptString($setting->value);
            
            // Update directly in database to bypass the model's encryption
            \DB::table('settings')
                ->where('key', $key)
                ->update(['value' => $encryptedValue]);

            $this->info("Setting '{$key}' encrypted successfully.");
        }

        // Clear cache
        app(\App\Services\SettingsService::class)->clearCache();
        $this->info('Settings cache cleared.');

        $this->info('Done!');

        return Command::SUCCESS;
    }
}

