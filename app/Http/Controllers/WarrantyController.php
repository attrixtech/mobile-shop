<?php

namespace App\Http\Controllers;

use App\Models\ImeiRecord;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function index(Request $request)
    {
        $searchImei = trim($request->input('imei', ''));
        $foundRecord = null;

        if (!empty($searchImei)) {
            $foundRecord = ImeiRecord::with(['product', 'variant', 'customer', 'sale', 'branch'])
                ->where(function ($q) use ($searchImei) {
                    $q->where('imei1', $searchImei)
                      ->orWhere('imei2', $searchImei)
                      ->orWhere('serial_number', $searchImei);
                })
                ->first();
        }

        // Active warranties
        $activeWarranties = ImeiRecord::with(['product', 'customer', 'sale'])
            ->where('status', 'sold')
            ->whereDate('warranty_end', '>=', now()->toDateString())
            ->latest('sold_at')
            ->paginate(15);

        return view('warranty.index', compact('searchImei', 'foundRecord', 'activeWarranties'));
    }
}
