<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'product_id',
        'product_name',
        'unit_label',
        'conversion_qty',
        'qty',
        'unit_price',
        'discount_pct',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'unit_price'     => 'decimal:2',
            'discount_pct'   => 'decimal:2',
            'total'          => 'decimal:2',
            'conversion_qty' => 'decimal:4',
        ];
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
