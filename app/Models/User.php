<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'notification_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'notification_preferences' => 'boolean',
    ];

    /**
     * Check if the user is an admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is an editor
     *
     * @return bool
     */
    public function isEditor()
    {
        return $this->role === 'editor';
    }

    /**
     * Get the events created by this user
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the categories created by this user
     */
    public function categories()
    {
        return $this->hasMany(Category::class, 'created_by');
    }

    /**
     * Get the comments posted by this user
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the media uploaded by this user
     */
    public function media()
    {
        return $this->hasMany(Media::class, 'uploaded_by');
    }

    /**
     * Get the notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the likes by this user
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function notificationPreference()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    /**
     * Get the business card for this user
     */
    public function businessCard()
    {
        return $this->hasOne(BusinessCard::class);
    }

    /**
     * Get the card shares for this user
     */
    public function cardShares()
    {
        return $this->hasManyThrough(CardShare::class, BusinessCard::class);
    }

    /**
     * Generate a unique username
     */
    public static function generateUsername(string $name): string
    {
        $baseUsername = Str::slug($name);
        $username = $baseUsername;
        $counter = 1;

        while (self::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Get the public card URL
     */
    public function getPublicCardUrl(): string
    {
        if ($this->username) {
            return url("/carte/{$this->username}");
        }
        return '';
    }

    /**
     * Get the user's profile image
     */
    public function getProfileImage(): string
    {
        // Check if user has a business card with a logo
        if ($this->businessCard && $this->businessCard->logo) {
            return asset('storage/' . $this->businessCard->logo);
        }
        
        // Return a default avatar
        return asset('images/default-avatar.svg');
    }

    /**
     * Get the user's profile image with fallback
     */
    public function getProfileImageUrl(): string
    {
        return $this->getProfileImage();
    }
}
