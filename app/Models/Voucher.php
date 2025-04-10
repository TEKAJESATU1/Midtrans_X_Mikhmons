<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'username',
        'password',
        'profile',
        'duration',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Helper method untuk cek apakah voucher masih valid
    public function isValid()
    {
        return !$this->isExpired();
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    // Otomatis set expires_at saat set duration
    public function setDurationAttribute($value)
    {
        $this->attributes['duration'] = $value;
        $this->attributes['expires_at'] = now()->addMinutes($value);
    }
}
