<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessCardExchange extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'sender_card_id',
        'receiver_card_id',
        'event_id',
        'exchange_method',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    // Relations
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function senderCard()
    {
        return $this->belongsTo(BusinessCard::class, 'sender_card_id');
    }

    public function receiverCard()
    {
        return $this->belongsTo(BusinessCard::class, 'receiver_card_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Scopes
    public function scopeByEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('sender_id', $userId)
              ->orWhere('receiver_id', $userId);
        });
    }

    public function scopeByExchangeMethod($query, $method)
    {
        return $query->where('exchange_method', $method);
    }

    // Méthodes
    public function isDigitalExchange()
    {
        return $this->exchange_method === 'digital';
    }

    public function isQrCodeExchange()
    {
        return $this->exchange_method === 'qr_code';
    }

    public function isNfcExchange()
    {
        return $this->exchange_method === 'nfc';
    }

    public function getExchangeMethodLabel()
    {
        return match($this->exchange_method) {
            'digital' => 'Échange numérique',
            'qr_code' => 'QR Code',
            'nfc' => 'NFC',
            default => 'Autre'
        };
    }

    public function getMetadataValue($key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setMetadataValue($key, $value)
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
        $this->save();
    }
} 