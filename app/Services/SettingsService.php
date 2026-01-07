<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_KEY = 'app_settings';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get a setting value with caching
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->getAllCached();

        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value and clear cache
     */
    public function set(string $key, mixed $value): void
    {
        Setting::set($key, $value);
        $this->clearCache();
    }

    /**
     * Set multiple settings at once
     */
    public function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
        $this->clearCache();
    }

    /**
     * Check if a setting exists
     */
    public function has(string $key): bool
    {
        $settings = $this->getAllCached();

        return isset($settings[$key]);
    }

    /**
     * Delete a setting and clear cache
     */
    public function forget(string $key): void
    {
        Setting::forget($key);
        $this->clearCache();
    }

    /**
     * Get all settings with caching
     */
    public function all(): array
    {
        return $this->getAllCached();
    }

    /**
     * Clear the settings cache
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get all settings from cache or database
     */
    private function getAllCached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::getAllSettings();
        });
    }

    /**
     * Get Paperless API URL
     */
    public function getPaperlessUrl(): string
    {
        return $this->get('paperless.url', 'http://localhost:8010');
    }

    /**
     * Get Paperless API Key
     * Always fetch directly from database to ensure proper decryption
     */
    public function getPaperlessApiKey(): string
    {
        return Setting::get('paperless.api_key', '');
    }

    /**
     * Set Paperless credentials
     */
    public function setPaperlessCredentials(string $url, string $apiKey): void
    {
        $this->setMany([
            'paperless.url' => $url,
            'paperless.api_key' => $apiKey,
        ]);
    }

    /**
     * Get all Paperless settings
     */
    public function getPaperlessSettings(): array
    {
        return [
            'url' => $this->getPaperlessUrl(),
            'api_key' => $this->getPaperlessApiKey(),
        ];
    }

    /**
     * Get document lock duration in seconds
     * Minimum is 5 seconds to prevent infinite loops with Paperless
     */
    public function getDocumentLockDuration(): int
    {
        $duration = $this->get('document.lock_duration', 60);

        // Ensure minimum of 5 seconds
        return max(5, (int) $duration);
    }

    /**
     * Set document lock duration in seconds
     * Minimum is 5 seconds to prevent infinite loops with Paperless
     */
    public function setDocumentLockDuration(int $seconds): void
    {
        // Ensure minimum of 5 seconds
        $seconds = max(5, $seconds);

        $this->set('document.lock_duration', $seconds);
    }

    /**
     * Get Ollama URL
     */
    public function getOllamaUrl(): string
    {
        return $this->get('ollama.url', 'http://localhost:11434');
    }

    /**
     * Get Ollama Model
     */
    public function getOllamaModel(): string
    {
        return $this->get('ollama.model', 'llama3.1');
    }

    /**
     * Set Ollama credentials
     */
    public function setOllamaCredentials(string $url, string $model): void
    {
        $this->setMany([
            'ollama.url' => $url,
            'ollama.model' => $model,
        ]);
    }

    /**
     * Get all Ollama settings
     */
    public function getOllamaSettings(): array
    {
        return [
            'url' => $this->getOllamaUrl(),
            'model' => $this->getOllamaModel(),
        ];
    }

    /**
     * Get document processing mode (webhook or polling)
     */
    public function getDocumentProcessingMode(): string
    {
        return $this->get('document.processing_mode', 'webhook');
    }

    /**
     * Set document processing mode
     */
    public function setDocumentProcessingMode(string $mode): void
    {
        if (!in_array($mode, ['webhook', 'polling'])) {
            throw new \InvalidArgumentException("Invalid processing mode: {$mode}. Must be 'webhook' or 'polling'.");
        }

        $this->set('document.processing_mode', $mode);
    }

    /**
     * Get polling interval in minutes
     * Minimum is 1 minute
     */
    public function getPollingInterval(): int
    {
        $interval = $this->get('document.polling_interval', 5);

        // Ensure minimum of 1 minute
        return max(1, (int) $interval);
    }

    /**
     * Set polling interval in minutes
     * Minimum is 1 minute
     */
    public function setPollingInterval(int $minutes): void
    {
        // Ensure minimum of 1 minute
        $minutes = max(1, $minutes);

        $this->set('document.polling_interval', $minutes);
    }

    /**
     * Get last poll timestamp
     */
    public function getLastPollTimestamp(): ?string
    {
        return $this->get('document.last_poll_timestamp', null);
    }

    /**
     * Set last poll timestamp
     */
    public function setLastPollTimestamp(string $timestamp): void
    {
        $this->set('document.last_poll_timestamp', $timestamp);
    }

    /**
     * Check if polling mode is enabled
     */
    public function isPollingEnabled(): bool
    {
        return $this->getDocumentProcessingMode() === 'polling';
    }

    /**
     * Check if webhook mode is enabled
     */
    public function isWebhookEnabled(): bool
    {
        return $this->getDocumentProcessingMode() === 'webhook';
    }

    /**
     * Check if login is enabled
     */
    public function isLoginEnabled(): bool
    {
        return (bool) $this->get('auth.login_enabled', false);
    }

    /**
     * Enable or disable login
     */
    public function setLoginEnabled(bool $enabled): void
    {
        $this->set('auth.login_enabled', $enabled);
    }

    /**
     * Check if setup wizard has been completed
     */
    public function isSetupCompleted(): bool
    {
        return (bool) $this->get('setup.completed', false);
    }

    /**
     * Mark setup wizard as completed
     */
    public function markSetupCompleted(): void
    {
        $this->set('setup.completed', true);
    }

    /**
     * Get maximum DSL length
     */
    public function getMaxDslLength(): int
    {
        return (int) $this->get('limits.max_dsl_length', 10000);
    }

    /**
     * Get maximum LET count
     */
    public function getMaxLetCount(): int
    {
        return (int) $this->get('limits.max_let_count', 50);
    }

    /**
     * Get maximum DO count
     */
    public function getMaxDoCount(): int
    {
        return (int) $this->get('limits.max_do_count', 50);
    }

    /**
     * Get maximum nesting depth
     */
    public function getMaxNestingDepth(): int
    {
        return (int) $this->get('limits.max_nesting_depth', 10);
    }

    /**
     * Get all rule limits
     */
    public function getRuleLimits(): array
    {
        return [
            'max_dsl_length' => $this->getMaxDslLength(),
            'max_let_count' => $this->getMaxLetCount(),
            'max_do_count' => $this->getMaxDoCount(),
            'max_nesting_depth' => $this->getMaxNestingDepth(),
        ];
    }
}

