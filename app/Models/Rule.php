<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $fillable = [
        'name',
        'rule',
        'enabled',
        'on_create',
        'on_change',
        'order',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'on_create' => 'boolean',
        'on_change' => 'boolean',
        'order' => 'integer',
    ];
}
