<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnitConversion extends Model
{
    protected $fillable = [
        'product_id',
        'unit_name',
        'conversion_qty',
        'sell_price',
        'buy_price',
    ];

    protected function casts(): array
    {
        return [
            'conversion_qty' => 'decimal:4',
            'sell_price'     => 'decimal:2',
            'buy_price'      => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
