<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('active_branch_id');
        $query = Product::with(['category', 'brand', 'variants'])->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('barcode', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%");
            });
        }

        $products = $query->paginate(15);
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('products.index', compact('products', 'categories', 'brands', 'branchId'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'sku' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:50',
            'type' => 'required|in:serialized,standard,service,bundle',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'processor' => 'nullable|string',
            'battery' => 'nullable|string',
            'display_size' => 'nullable|string',
            'os' => 'nullable|string',
            'network' => 'nullable|string',
            'warranty_months' => 'nullable|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'hsn_code' => 'nullable|string|max:20',
            'gst_rate' => 'required|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'reorder_qty' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:20',
        ]);

        $product = Product::create($validated);

        // Check if variants were submitted
        if ($request->has('variants') && is_array($request->variants)) {
            foreach ($request->variants as $variant) {
                if (!empty($variant['name'])) {
                    $product->variants()->create([
                        'tenant_id' => $product->tenant_id,
                        'name' => $variant['name'],
                        'ram' => $variant['ram'] ?? null,
                        'storage' => $variant['storage'] ?? null,
                        'color' => $variant['color'] ?? null,
                        'purchase_price' => $variant['purchase_price'] ?? $product->purchase_price,
                        'selling_price' => $variant['selling_price'] ?? $product->selling_price,
                        'mrp' => $variant['mrp'] ?? $product->mrp,
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'sku' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:50',
            'type' => 'required|in:serialized,standard,service,bundle',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'processor' => 'nullable|string',
            'battery' => 'nullable|string',
            'display_size' => 'nullable|string',
            'os' => 'nullable|string',
            'network' => 'nullable|string',
            'warranty_months' => 'nullable|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'hsn_code' => 'nullable|string|max:20',
            'gst_rate' => 'required|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'reorder_qty' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
