<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RuleExecutionLog extends Model
{
    protected $fillable = [
        'job_id',
        'document_id',
        'rule_id',
        'rule_name',
        'rule_order',
        'event_type',
        'status',
        'error_message',
        'trace',
        'document_modified',
        'executed_at',
    ];

    protected $casts = [
        'trace' => 'array',
        'document_modified' => 'boolean',
        'executed_at' => 'datetime',
        'rule_order' => 'integer',
    ];

    /**
     * Get the rule that was executed
     */
    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

    /**
     * Scope to filter by document ID
     */
    public function scopeForDocument($query, int $documentId)
    {
        return $query->where('document_id', $documentId);
    }

    /**
     * Scope to filter by rule ID
     */
    public function scopeForRule($query, int $ruleId)
    {
        return $query->where('rule_id', $ruleId);
    }

    /**
     * Scope to filter by event type
     */
    public function scopeEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}

