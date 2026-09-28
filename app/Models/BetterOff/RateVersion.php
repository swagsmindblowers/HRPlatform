<?php

namespace App\Models\BetterOff;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RateVersion extends Model
{
    use HasFactory;

    protected $table = 'betteroff_rate_versions';

    protected $fillable = [
        'key',
        'value',
        'unit',
        'effective_from',
        'effective_to',
        'source_url',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'verified_at' => 'datetime',
    ];
}
