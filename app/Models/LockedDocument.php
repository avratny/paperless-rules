<?php

namespace App\Models;

use App\Services\SettingsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LockedDocument extends Model
{
    protected $fillable = [
        'document_id',
        'locked_at',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
    ];

    /**
     * Get the lock duration in seconds from settings
     */
    private static function getLockDuration(): int
    {
        $settingsService = app(SettingsService::class);
        return $settingsService->getDocumentLockDuration();
    }

    /**
     * Check if a document is currently locked
     */
    public static function isLocked(int $documentId): bool
    {
        $lockDuration = self::getLockDuration();
        $lockThreshold = Carbon::now()->subSeconds($lockDuration);

        return self::where('document_id', $documentId)
            ->where('locked_at', '>', $lockThreshold)
            ->exists();
    }

    /**
     * Lock a document
     */
    public static function lock(int $documentId): void
    {
        self::updateOrCreate(
            ['document_id' => $documentId],
            ['locked_at' => Carbon::now()]
        );
    }

    /**
     * Unlock a document
     */
    public static function unlock(int $documentId): void
    {
        self::where('document_id', $documentId)->delete();
    }

    /**
     * Clean up old locks
     */
    public static function cleanup(): int
    {
        $lockDuration = self::getLockDuration();
        $threshold = Carbon::now()->subSeconds($lockDuration);

        return self::where('locked_at', '<', $threshold)->delete();
    }
}
