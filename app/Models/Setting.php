<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * List of keys that should be encrypted
     */
    private static array $encryptedKeys = [
        'paperless.api_key',
    ];

    /**
     * Check if a key should be encrypted
     */
    private static function shouldEncrypt(string $key): bool
    {
        return in_array($key, self::$encryptedKeys);
    }

    /**
     * Get a setting value by key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        // Decrypt if needed
        if (self::shouldEncrypt($key) && $setting->value) {
            try {
                return Crypt::decryptString($setting->value);
            } catch (\Exception $e) {
                // If decryption fails, return the raw value (for backwards compatibility)
                return $setting->value;
            }
        }

        return $setting->value;
    }

    /**
     * Set a setting value by key
     */
    public static function set(string $key, mixed $value): void
    {
        // Encrypt if needed
        if (self::shouldEncrypt($key) && $value) {
            $value = Crypt::encryptString($value);
        }

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Check if a setting exists
     */
    public static function has(string $key): bool
    {
        return static::where('key', $key)->exists();
    }

    /**
     * Delete a setting by key
     */
    public static function forget(string $key): void
    {
        static::where('key', $key)->delete();
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllSettings(): array
    {
        $settings = static::query()->get();
        $result = [];

        foreach ($settings as $setting) {
            $key = $setting->key;
            $value = $setting->value;

            // Decrypt if needed
            if (self::shouldEncrypt($key) && $value) {
                try {
                    $value = Crypt::decryptString($value);
                } catch (\Exception $e) {
                    // If decryption fails, use raw value
                }
            }

            $result[$key] = $value;
        }

        return $result;
    }
}
