<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'labor_hours',
        'profit_margin',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'labor_hours'   => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'material_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Calcula o custo total do produto:
     * Σ (quantidade_material × preço_unitário_material) + (horas_mão_obra × valor_hora_artesão)
     */
    public function getTotalCostAttribute(): float
    {
        $materialsCost = $this->materials->sum(function (Material $material) {
            return $material->pivot->quantity * $material->cost_price;
        });

        $laborCost = $this->labor_hours * ($this->user->hourly_rate ?? 0);

        return round($materialsCost + $laborCost, 2);
    }

    /**
     * Calcula o preço de venda sugerido aplicando a margem de lucro.
     */
    public function getSuggestedPriceAttribute(): float
    {
        $cost = $this->total_cost;
        return round($cost * (1 + $this->profit_margin / 100), 2);
    }

    /**
     * Garante que os materiais estejam carregados antes de calcular custo.
     */
    public function calculateCost(): float
    {
        if (! $this->relationLoaded('materials') || ! $this->relationLoaded('user')) {
            $this->load('materials', 'user');
        }

        return $this->total_cost;
    }
}
