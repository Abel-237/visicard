<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CardShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_card_id',
        'share_token',
        'share_method',
        'recipient_email',
        'recipient_name',
        'custom_message',
        'shared_at',
        'viewed_at',
        'viewer_ip',
        'viewer_user_agent',
        'is_active',
    ];

    protected $casts = [
        'shared_at' => 'datetime',
        'viewed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the business card that owns the share.
     */
    public function businessCard()
    {
        return $this->belongsTo(BusinessCard::class);
    }

    /**
     * Generate a unique share token
     */
    public static function generateShareToken(): string
    {
        do {
            $token = Str::random(32);
        } while (self::where('share_token', $token)->exists());

        return $token;
    }

    /**
     * Mark the share as viewed
     */
    public function markAsViewed(string $ip = null, string $userAgent = null): void
    {
        $this->update([
            'viewed_at' => now(),
            'viewer_ip' => $ip,
            'viewer_user_agent' => $userAgent,
        ]);
    }

    /**
     * Get the share URL
     */
    public function getShareUrl(): string
    {
        return url("/carte/{$this->share_token}");
    }

    /**
     * Get the QR code data
     */
    public function getQrCodeData(): string
    {
        return $this->getShareUrl();
    }
} 