<?php

namespace App\Queries;

use Illuminate\Database\Eloquent\Builder;

class MotorcycleQuery extends Builder
{
    public function filter(array $filters): static
    {
        $this->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        });

        $this->when($filters['make'] ?? null, fn ($q, $v) => $q->where('make', $v));
        $this->when($filters['model'] ?? null, fn ($q, $v) => $q->where('model', 'like', "%{$v}%"));
        $this->when($filters['year_min'] ?? null, fn ($q, $v) => $q->where('year', '>=', $v));
        $this->when($filters['year_max'] ?? null, fn ($q, $v) => $q->where('year', '<=', $v));
        $this->when($filters['price_min'] ?? null, fn ($q, $v) => $q->where('price', '>=', $v));
        $this->when($filters['price_max'] ?? null, fn ($q, $v) => $q->where('price', '<=', $v));
        $this->when(! empty($filters['in_stock']), fn ($q) => $q->where('stock_quantity', '>', 0));

        return $this;
    }
}
