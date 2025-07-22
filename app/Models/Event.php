<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'category_id',
        'user_id',
        'event_date',
        'location',
        'status',
        'featured',
        'published_at',
        'logo_path',
        'background_image_path',
        'theme_color',
        'custom_css',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_date' => 'datetime',
        'published_at' => 'datetime',
        'featured' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }
        });
    }

    /**
     * Get the user who created this event
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of this event
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the comments on this event
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the media of this event
     */
    public function media()
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Get the notifications related to this event
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the tags of this event
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the likes for this event
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Scope a query to only include published events
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include featured events
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to only include events by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Check if the event is published
     */
    public function isPublished()
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    /**
     * Increment the view count
     */
    public function incrementViewCount()
    {
        $this->views++;
        return $this->save();
    }

    /**
     * Relation avec les participants
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_user')
                    ->withPivot('status', 'created_at')
                    ->withTimestamps();
    }

    /**
     * Relation avec les cartes de visite échangées
     */
    public function businessCards()
    {
        return $this->hasMany(BusinessCard::class);
    }

    /**
     * Relation avec les invitations
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Vérifie si l'événement est actif
     */
    public function isActive()
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    /**
     * Vérifie si l'événement est à venir
     */
    public function isUpcoming()
    {
        return $this->event_date > now();
    }

    /**
     * Vérifie si l'utilisateur est participant
     */
    public function hasParticipant($userId)
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    /**
     * Ajoute un participant
     */
    public function addParticipant($userId, $status = 'pending')
    {
        return $this->participants()->attach($userId, ['status' => $status]);
    }

    /**
     * Supprime un participant
     */
    public function removeParticipant($userId)
    {
        return $this->participants()->detach($userId);
    }

    /**
     * Met à jour le statut d'un participant
     */
    public function updateParticipantStatus($userId, $status)
    {
        return $this->participants()->updateExistingPivot($userId, ['status' => $status]);
    }

    /**
     * Génère un QR code pour l'accès à l'événement
     */
    public function generateAccessQRCode($userId)
    {
        return route('events.access', ['event' => $this->id, 'user' => $userId]);
    }

    /**
     * Récupère les statistiques de l'événement
     */
    public function getStats()
    {
        return [
            'total_participants' => $this->participants()->count(),
            'active_participants' => $this->participants()
                                        ->where('last_login_at', '>=', now()->subDay())
                                        ->count(),
            'cards_exchanged' => $this->businessCards()->count(),
            'interactions' => $this->comments()->count() + $this->likes()->count(),
        ];
    }
}
