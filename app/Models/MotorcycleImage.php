<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MotorcycleImage extends Model
{
    protected $fillable = ['motorcycle_id', 'path', 'alt_text', 'sort_order', 'is_primary'];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk(self::disk())->url($this->path);
    }

    public static function disk(): string
    {
        // 'local' is private-only; map it to the public disk for local dev.
        $disk = config('filesystems.default');
        return $disk === 'local' ? 'public' : $disk;
    }
}
