<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreProfile extends Model
{
    protected $fillable = ['name', 'address', 'phone', 'email', 'owner_name', 'bank_accounts', 'logo'];

    protected function casts(): array
    {
        return [
            'bank_accounts' => 'array',
        ];
    }
}
