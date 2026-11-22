<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->tenant_id) {
                session(['active_tenant_id' => $user->tenant_id]);

                if (!session()->has('active_branch_id')) {
                    $branchId = $user->branch_id;
                    if (!$branchId) {
                        $mainBranch = Branch::withoutGlobalScope('tenant')
                            ->where('tenant_id', $user->tenant_id)
                            ->where('is_main', true)
                            ->first();
                        $branchId = $mainBranch?->id;
                    }
                    if ($branchId) {
                        session(['active_branch_id' => $branchId]);
                    }
                }
            }
        }

        return $next($request);
    }
}
