<?php

namespace App\Models;

use App\Queries\MotorcycleQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Motorcycle extends Model
{
    use HasSlug;

    protected $fillable = [
        'make', 'model', 'year', 'color', 'engine_size_cc', 'mileage_miles',
        'price', 'stock_quantity', 'description', 'slug',
        'meta_title', 'meta_description', 'is_featured', 'video_path',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'stock_quantity' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn ($model) => "{$model->year} {$model->make} {$model->model}")
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(100);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function images(): HasMany
    {
        return $this->hasMany(MotorcycleImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(MotorcycleImage::class)->where('is_primary', true)->limit(1);
    }

    public function newEloquentBuilder($query): MotorcycleQuery
    {
        return new MotorcycleQuery($query);
    }

    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title ?: "{$this->year} {$this->make} {$this->model}";
    }

    public function getSeoDescriptionAttribute(): string
    {
        return $this->meta_description ?: Str::limit($this->description ?? '', 155);
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video_path
            ? \Storage::disk(MotorcycleImage::disk())->url($this->video_path)
            : null;
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }
}
