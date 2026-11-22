<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsedDevice extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'purchase_cost' => 'decimal:2',
            'repair_cost' => 'decimal:2',
            'expected_selling_price' => 'decimal:2',
            'sold_price' => 'decimal:2',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function exchange()
    {
        return $this->belongsTo(ExchangeTransaction::class, 'exchange_id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
