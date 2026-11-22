<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function isSubscriptionActive(): bool
    {
        if ($this->status === 'active') {
            return $this->subscription_ends_at ? $this->subscription_ends_at->isFuture() : true;
        }
        if ($this->status === 'trial') {
            return $this->trial_ends_at ? $this->trial_ends_at->isFuture() : true;
        }
        return false;
    }
}
