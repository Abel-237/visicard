<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
    ];

    /**
     * Get the user who created this like
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent likeable model (event or comment)
     */
    public function likeable()
    {
        return $this->morphTo();
    }
}
