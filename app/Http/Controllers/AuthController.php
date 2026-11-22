<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->tenant_id) {
                session(['active_tenant_id' => $user->tenant_id]);
                if ($user->branch_id) {
                    session(['active_branch_id' => $user->branch_id]);
                } else {
                    $mainBranch = Branch::withoutGlobalScope('tenant')
                        ->where('tenant_id', $user->tenant_id)
                        ->where('is_main', true)
                        ->first();
                    if ($mainBranch) {
                        session(['active_branch_id' => $mainBranch->id]);
                    }
                }
            }

            if ($user->role === 'super_admin' && !$user->tenant_id) {
                return redirect()->route('superadmin.dashboard');
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function switchBranch(Request $request)
    {
        $branchId = (int) $request->input('branch_id');
        $user = Auth::user();

        if ($user->canAccessBranch($branchId)) {
            session(['active_branch_id' => $branchId]);
            return back()->with('success', 'Branch switched successfully.');
        }

        return back()->with('error', 'You are not authorized to access this branch.');
    }
}
