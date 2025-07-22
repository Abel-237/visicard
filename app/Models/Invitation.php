<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'email',
        'token',
        'status',
        'message',
        'sent_at',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Relation avec l'événement
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relation avec l'utilisateur (si l'invitation a été acceptée)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Génère un token unique pour l'invitation
     */
    public function generateToken()
    {
        $this->token = md5(uniqid($this->email, true));
        return $this->token;
    }

    /**
     * Marque l'invitation comme envoyée
     */
    public function markAsSent()
    {
        $this->status = 'sent';
        $this->sent_at = now();
        return $this->save();
    }

    /**
     * Marque l'invitation comme acceptée
     */
    public function markAsAccepted()
    {
        $this->status = 'accepted';
        $this->accepted_at = now();
        return $this->save();
    }

    /**
     * Marque l'invitation comme rejetée
     */
    public function markAsRejected()
    {
        $this->status = 'rejected';
        $this->rejected_at = now();
        return $this->save();
    }

    /**
     * Vérifie si l'invitation est en attente
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Vérifie si l'invitation a été envoyée
     */
    public function isSent()
    {
        return $this->status === 'sent';
    }

    /**
     * Vérifie si l'invitation a été acceptée
     */
    public function isAccepted()
    {
        return $this->status === 'accepted';
    }

    /**
     * Vérifie si l'invitation a été rejetée
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Vérifie si l'invitation est expirée
     */
    public function isExpired()
    {
        return $this->sent_at && $this->sent_at->addDays(7)->isPast();
    }

    /**
     * Génère le lien d'invitation
     */
    public function getInvitationLink()
    {
        return route('invitations.accept', ['token' => $this->token]);
    }
} 