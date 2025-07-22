<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Get the events associated with this tag
     */
    public function events()
    {
        return $this->belongsToMany(Event::class);
    }

    /**
     * Get the number of events with this tag
     */
    public function getEventsCountAttribute()
    {
        return $this->events()->count();
    }

    /**
     * Get the number of published events with this tag
     */
    public function getPublishedEventsCountAttribute()
    {
        return $this->events()->where('status', 'published')->count();
    }
}
