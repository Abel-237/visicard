<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'file_path',
        'file_type',
        'mime_type',
        'size',
        'event_id',
        'uploaded_by',
    ];

    /**
     * Get the user who uploaded this media
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the event this media belongs to
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the URL for the media
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Check if the media is an image
     */
    public function isImage()
    {
        return $this->file_type === 'image';
    }

    /**
     * Check if the media is a video
     */
    public function isVideo()
    {
        return $this->file_type === 'video';
    }

    /**
     * Check if the media is a document
     */
    public function isDocument()
    {
        return $this->file_type === 'document';
    }

    /**
     * Get the formatted file size
     */
    public function getFormattedSizeAttribute()
    {
        if ($this->size < 1024) {
            return $this->size . ' B';
        } elseif ($this->size < 1048576) {
            return round($this->size / 1024, 2) . ' KB';
        } else {
            return round($this->size / 1048576, 2) . ' MB';
        }
    }
}
