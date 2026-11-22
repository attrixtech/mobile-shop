<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function index(Request $request)
    {
        // First active tenant for demo storefront
        $tenant = Tenant::with('company')->first();
        if (!$tenant) {
            return redirect()->route('login');
        }

        $query = Product::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['brand', 'category', 'variants']);

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', fn($q) => $q->where('slug', $request->brand));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'like', "%{$s}%");
        }

        $products = $query->latest('id')->paginate(16)->withQueryString();

        $categories = Category::withoutGlobalScope('tenant')->where('tenant_id', $tenant->id)->where('is_active', true)->withCount(['products' => fn($q) => $q->withoutGlobalScope('tenant')->where('is_active', true)])->get();
        $brands = Brand::withoutGlobalScope('tenant')->where('tenant_id', $tenant->id)->where('is_active', true)->get();
        $branches = \App\Models\Branch::withoutGlobalScope('tenant')->where('tenant_id', $tenant->id)->where('is_active', true)->get();
        $company = $tenant->company;

        return view('storefront.index', compact('tenant', 'company', 'products', 'categories', 'brands', 'branches'));
    }

    public function show(int $id)
    {
        $product = Product::withoutGlobalScope('tenant')->with(['brand', 'category', 'variants'])->findOrFail($id);
        $tenant = Tenant::with('company')->findOrFail($product->tenant_id);
        $company = $tenant->company;

        return view('storefront.show', compact('product', 'tenant', 'company'));
    }
}
