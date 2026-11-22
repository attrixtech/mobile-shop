<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'mrp' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'gst_rate' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function stockBalances()
    {
        return $this->hasMany(StockBalance::class);
    }

    public function imeiRecords()
    {
        return $this->hasMany(ImeiRecord::class);
    }

    public function isSerialized(): bool
    {
        return $this->type === 'serialized';
    }

    public function totalStockForBranch(?int $branchId = null): int
    {
        $query = $this->stockBalances();
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        return (int) $query->sum('quantity');
    }
}
