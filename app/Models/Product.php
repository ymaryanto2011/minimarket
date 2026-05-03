<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category_id',
        'retail_price',
        'wholesale_price',
        'min_wholesale_qty',
        'stock',
        'min_stock',
        'unit',
        'barcode',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'retail_price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function unitConversions()
    {
        return $this->hasMany(ProductUnitConversion::class);
    }

    /**
     * Returns all available units: base unit first, then conversions.
     * Each row: ['unit_name', 'conversion_qty', 'sell_price', 'buy_price']
     */
    public function allUnits(): array
    {
        $base = [
            'unit_name'      => $this->unit,
            'conversion_qty' => 1,
            'sell_price'     => (float) $this->retail_price,
            'buy_price'      => 0,
        ];

        $extras = $this->unitConversions->map(fn($c) => [
            'unit_name'      => $c->unit_name,
            'conversion_qty' => (float) $c->conversion_qty,
            'sell_price'     => (float) $c->sell_price,
            'buy_price'      => (float) $c->buy_price,
        ])->values()->all();

        return array_merge([$base], $extras);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }
}
