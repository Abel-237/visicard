<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
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
        'description',
        'color',
        'created_by',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get the user who created this category
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the events in this category
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the number of events in this category
     */
    public function getEventsCountAttribute()
    {
        return $this->events()->count();
    }

    /**
     * Get the number of published events in this category
     */
    public function getPublishedEventsCountAttribute()
    {
        return $this->events()->where('status', 'published')->count();
    }
}
