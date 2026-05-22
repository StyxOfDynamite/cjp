<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MotorcycleImage extends Model
{
    protected $fillable = ['motorcycle_id', 'path', 'alt_text', 'sort_order', 'is_primary', 'has_thumbnail'];

    protected $casts = [
        'is_primary'     => 'boolean',
        'has_thumbnail'  => 'boolean',
        'sort_order'     => 'integer',
    ];

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk(self::disk())->url($this->path);
    }

    // Thumbnail is stored alongside the full image with a _thumb suffix.
    // Falls back to the full image if no thumbnail exists yet.
    public function getThumbnailUrlAttribute(): string
    {
        if (! $this->has_thumbnail) {
            return $this->url;
        }

        $thumb = preg_replace('/\.webp$/', '_thumb.webp', $this->path);
        return Storage::disk(self::disk())->url($thumb);
    }

    public static function disk(): string
    {
        // 'local' is private-only; map it to the public disk for local dev.
        $disk = config('filesystems.default');
        return $disk === 'local' ? 'public' : $disk;
    }
}
