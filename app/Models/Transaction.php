<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'snap_token',
        'status',
        'price',
        'username',
        'password',
        'profile',
        'duration',
        'payload'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
        'price' => 'integer',
        'duration' => 'integer'
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';

    /**
     * Scope for pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for successful transactions
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    /**
     * Check if transaction is pending
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if transaction is successful
     */
    public function isSuccess()
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * Check if transaction is failed
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if transaction is expired
     */
    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    /**
     * Get the voucher associated with the transaction.
     */
    public function voucher()
    {
        return $this->hasOne(Voucher::class);
    }
}
