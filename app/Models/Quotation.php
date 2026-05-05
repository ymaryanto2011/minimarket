<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    const ACTIVE_STATUSES   = ['draft', 'submit', 'approved'];
    const COMPLETE_STATUSES = ['paid'];

    protected $fillable = [
        'quotation_no',
        'to_name',
        'date',
        'valid_until',
        'subtotal',
        'discount',
        'tax_rate',
        'tax_amount',
        'total',
        'notes',
        'status',
        'is_custom',
        'transaction_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date'        => 'date',
            'valid_until' => 'date',
            'subtotal'    => 'decimal:2',
            'discount'    => 'decimal:2',
            'tax_rate'    => 'decimal:2',
            'tax_amount'  => 'decimal:2',
            'total'       => 'decimal:2',
        ];
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public static function generateQuotationNo(): string
    {
        $prefix = "QTN-" . date("Ymd") . "-";
        $last = self::where("quotation_no", "like", $prefix . "%")
            ->orderBy("id", "desc")->first();
        $seq = $last ? ((int) substr($last->quotation_no, -4)) + 1 : 1;
        return $prefix . str_pad($seq, 4, "0", STR_PAD_LEFT);
    }
}
