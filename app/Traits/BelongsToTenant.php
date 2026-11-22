<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->tenant_id)) {
                if (app()->bound('auth') && auth()->hasUser() && auth()->user()->tenant_id) {
                    $model->tenant_id = auth()->user()->tenant_id;
                } elseif (session()->has('active_tenant_id')) {
                    $model->tenant_id = session('active_tenant_id');
                }
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (app()->bound('auth') && auth()->hasUser()) {
                $user = auth()->user();
                if ($user->role === 'super_admin' && !session()->has('active_tenant_id')) {
                    return;
                }
                $tenantId = session('active_tenant_id', $user->tenant_id);
                if ($tenantId) {
                    $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenantId);
                }
            } elseif (session()->has('active_tenant_id')) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', session('active_tenant_id'));
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
