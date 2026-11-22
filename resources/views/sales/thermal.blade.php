<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $sale->invoice_number }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.25;
            color: #000;
            background: #fff;
            width: 76mm;
            margin: 0 auto;
            padding: 2mm 1mm;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .border-top { border-top: 1px dashed #000; }
        .border-bottom { border-bottom: 1px dashed #000; }
        .py-1 { padding-top: 3px; padding-bottom: 3px; }
        .my-1 { margin-top: 4px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th, td { padding: 2px 0; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 10px; text-align: center;">
        <button onclick="window.print()" style="padding: 5px 15px; font-weight: bold; cursor: pointer;">Print Receipt</button>
        <button onclick="window.close()" style="padding: 5px 10px; cursor: pointer;">Close</button>
    </div>

    <!-- Store Header -->
    <div class="text-center">
        <div class="font-bold" style="font-size: 14px;">{{ $company->name ?? 'WEBOTAPP MOBILES' }}</div>
        <div>{{ $sale->branch?->name }}</div>
        <div>{{ $sale->branch?->address }}</div>
        <div>{{ $sale->branch?->city }}, {{ $sale->branch?->state }}</div>
        <div>Phone: {{ $sale->branch?->phone }}</div>
        @if($company->gstin)
            <div class="font-bold">GSTIN: {{ $company->gstin }}</div>
        @endif
    </div>

    <div class="border-top my-1"></div>

    <!-- Bill Meta -->
    <div>
        <div><strong>Invoice:</strong> {{ $sale->invoice_number }}</div>
        <div><strong>Date:</strong> {{ $sale->sale_date->format('d/m/Y') }} {{ $sale->created_at->format('H:i') }}</div>
        <div><strong>Cashier:</strong> {{ $sale->creator?->name }}</div>
        <div><strong>Customer:</strong> {{ $sale->customer?->name ?: 'Walk-in' }} ({{ $sale->customer?->phone ?: '-' }})</div>
    </div>

    <div class="border-top border-bottom py-1 my-1">
        <table>
            <thead>
                <tr>
                    <th class="text-left">Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                    <tr>
                        <td colspan="4" class="font-bold">
                            {{ $item->product?->name }}
                            @if($item->variant)
                                ({{ $item->variant->name }})
                            @endif
                        </td>
                    </tr>
                    @if(!empty($item->imeis) && is_array($item->imeis))
                        @foreach($item->imeis as $im)
                            <tr>
                                <td colspan="4" style="font-size: 9px; padding-left: 5px;">
                                    IMEI: {{ $im }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    <tr>
                        <td style="font-size: 9px;">HSN: {{ $item->product?->hsn_code }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right font-bold">₹{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Calculations Summary -->
    <div class="text-right" style="font-size: 10px;">
        <div>Taxable Value: ₹{{ number_format($sale->subtotal - $sale->discount_amount, 2) }}</div>
        @if($sale->cgst > 0)
            <div>CGST: ₹{{ number_format($sale->cgst, 2) }}</div>
        @endif
        @if($sale->sgst > 0)
            <div>SGST: ₹{{ number_format($sale->sgst, 2) }}</div>
        @endif
        @if($sale->igst > 0)
            <div>IGST: ₹{{ number_format($sale->igst, 2) }}</div>
        @endif
        @if($sale->exchange_allowance > 0)
            <div class="font-bold">Trade-In Allowance: -₹{{ number_format($sale->exchange_allowance, 2) }}</div>
        @endif
        <div class="font-bold border-top py-1" style="font-size: 13px;">
            NET TOTAL: ₹{{ number_format($sale->grand_total, 2) }}
        </div>
        <div>Mode: <span class="font-bold" style="text-transform: uppercase;">{{ $sale->payment_method }}</span></div>
        <div>Paid: ₹{{ number_format($sale->paid_amount, 2) }}</div>
        @if($sale->due_amount > 0)
            <div class="font-bold">Balance Due: ₹{{ number_format($sale->due_amount, 2) }}</div>
        @endif
    </div>

    <div class="border-top my-1"></div>

    <div class="text-center" style="font-size: 9px;">
        <p>Manufacturer Warranty applies as per brand policy.</p>
        <p>Keep invoice & original box for warranty claims.</p>
        <p class="font-bold">THANK YOU FOR YOUR PURCHASE!</p>
        <p style="font-size: 8px;">Powered by Webotapp Mobile Shop ERP</p>
    </div>

</body>
</html>
