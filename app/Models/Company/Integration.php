<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Integration extends Model
{
    use HasFactory;

    const PROVIDER_XERO = 'xero';
    const PROVIDER_DEEL = 'deel';

    const STATUS_CONNECTED = 'connected';
    const STATUS_DISCONNECTED = 'disconnected';
    const STATUS_ERROR = 'error';

    protected $table = 'integrations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'provider',
        'status',
        'access_token',
        'refresh_token',
        'expires_at',
        'external_account_id',
        'connected_by',
        'connected_at',
        'last_error',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expires_at',
        'connected_at',
    ];

    /**
     * Encrypted at rest - these tokens grant access to the company's Xero
     * or Deel account and must never be stored or logged in plain text.
     *
     * @var array
     */
    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->status === self::STATUS_CONNECTED;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
