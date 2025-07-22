<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'position',
        'company',
        'industry',
        'email',
        'phone',
        'website',
        'address',
        'bio',
        'logo',
        'social_media',
    ];

    protected $casts = [
        'social_media' => 'array',
    ];

    /**
     * Get the user that owns the business card.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the card shares for this business card.
     */
    public function cardShares()
    {
        return $this->hasMany(CardShare::class);
    }

    /**
     * Get the public share URL
     */
    public function getPublicShareUrl(): string
    {
        return $this->user->getPublicCardUrl();
    }


} 