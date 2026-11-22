<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = ['id'];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
