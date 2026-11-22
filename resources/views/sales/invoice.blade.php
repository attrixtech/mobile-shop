<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice - {{ $sale->invoice_number }}</title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
            line-height: 1.35;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .border { border: 1px solid #cbd5e1; }
        .border-b { border-bottom: 1px solid #cbd5e1; }
        .border-t { border-top: 1px solid #cbd5e1; }
        .border-r { border-right: 1px solid #cbd5e1; }
        .bg-gray { background-color: #f8fafc; }
        .p-1 { padding: 4px; }
        .p-2 { padding: 8px; }
        .py-1 { padding-top: 4px; padding-bottom: 4px; }
        .px-2 { padding-left: 8px; padding-right: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; }
        th { background-color: #f1f5f9; font-size: 10px; font-weight: bold; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 16px; background: #4f46e5; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
            Print / Save as PDF
        </button>
        <button onclick="window.close()" style="padding: 6px 12px; background: #e2e8f0; border: none; border-radius: 4px; cursor: pointer; margin-left: 5px;">
            Close
        </button>
    </div>

    <!-- Main Invoice Box -->
    <div class="border">
        <!-- Top Tax Invoice Header -->
        <div class="p-2 border-b text-center bg-gray">
            <h1 class="uppercase font-bold" style="font-size: 16px; margin: 0; letter-spacing: 1px;">TAX INVOICE</h1>
            <div style="font-size: 10px; color: #64748b;">(Issued under Section 31 of Central Goods and Services Tax Act, 2017)</div>
        </div>

        <!-- Seller and Meta Grid -->
        <table>
            <tr>
                <td style="width: 55%; vertical-align: top;" class="p-2">
                    <div class="font-bold uppercase" style="font-size: 13px; color: #0f172a;">{{ $company->legal_name ?? $company->name ?? 'Webotapp Retail India Pvt Ltd' }}</div>
                    <div style="color: #475569;">{{ $sale->branch?->address }}</div>
                    <div style="color: #475569;">{{ $sale->branch?->city }}, {{ $sale->branch?->state }} - {{ $sale->branch?->pincode }}</div>
                    <div style="margin-top: 4px;"><strong>GSTIN:</strong> {{ $company->gstin ?? '27AABCU9603R1ZM' }} | <strong>PAN:</strong> {{ $company->pan ?? 'AABCU9603R' }}</div>
                    <div><strong>State:</strong> {{ $sale->branch?->state }} (Code: {{ $sale->branch?->state_code ?? '27' }})</div>
                    <div><strong>Contact:</strong> {{ $sale->branch?->phone }} | {{ $sale->branch?->email }}</div>
                </td>
                <td style="width: 45%; vertical-align: top;" class="p-2 bg-gray">
                    <table style="border: none;">
                        <tr style="border: none;"><td style="border: none; padding: 2px 0;"><strong>Invoice No:</strong></td><td style="border: none; padding: 2px 0; font-weight: bold; color: #4f46e5;">{{ $sale->invoice_number }}</td></tr>
                        <tr style="border: none;"><td style="border: none; padding: 2px 0;"><strong>Invoice Date:</strong></td><td style="border: none; padding: 2px 0;">{{ $sale->sale_date->format('d-M-Y') }}</td></tr>
                        <tr style="border: none;"><td style="border: none; padding: 2px 0;"><strong>Branch Code:</strong></td><td style="border: none; padding: 2px 0;">{{ $sale->branch?->code }}</td></tr>
                        <tr style="border: none;"><td style="border: none; padding: 2px 0;"><strong>Place of Supply:</strong></td><td style="border: none; padding: 2px 0;">{{ $sale->customer?->state ?? $sale->branch?->state }}</td></tr>
                        <tr style="border: none;"><td style="border: none; padding: 2px 0;"><strong>Reverse Charge:</strong></td><td style="border: none; padding: 2px 0;">No</td></tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="p-2">
                    <div class="font-bold text-slate-700 uppercase" style="font-size: 10px; margin-bottom: 2px;">Details of Receiver / Billed to:</div>
                    <div class="font-bold" style="font-size: 12px;">{{ $sale->customer?->name ?: 'Walk-in Customer' }}</div>
                    <div>{{ $sale->customer?->address ?: 'Over the counter retail purchase' }}</div>
                    <div>{{ $sale->customer?->city }} {{ $sale->customer?->state }}</div>
                    <div><strong>Phone:</strong> {{ $sale->customer?->phone ?: '-' }} | <strong>GSTIN:</strong> {{ $sale->customer?->gstin ?: 'Unregistered Consumer' }}</div>
                </td>
            </tr>
        </table>

        <!-- Line Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">#</th>
                    <th style="width: 35%;">Description of Goods / Serial & IMEI</th>
                    <th style="width: 10%;">HSN/SAC</th>
                    <th style="width: 5%;">Qty</th>
                    <th style="width: 10%;">Rate (₹)</th>
                    <th style="width: 11%;">Taxable (₹)</th>
                    <th style="width: 12%;">CGST</th>
                    <th style="width: 12%;">SGST</th>
                    <th style="width: 12%;" class="text-right">Total (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $idx => $item)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>
                            <div class="font-bold">{{ $item->product?->name }}</div>
                            @if($item->variant)
                                <div style="font-size: 10px; color: #475569;">Variant: {{ $item->variant->name }}</div>
                            @endif
                            @if(!empty($item->imeis) && is_array($item->imeis))
                                <div style="margin-top: 2px; font-size: 9.5px; font-family: monospace; color: #4338ca;">
                                    @foreach($item->imeis as $im)
                                        <span>[IMEI: {{ $im }}]</span>
                                    @endforeach
                                </div>
                            @endif
                            <div style="font-size: 9px; color: #64748b;">Warranty: {{ $item->warranty_months }} Months Standard Brand Warranty</div>
                        </td>
                        <td class="text-center font-bold" style="font-size: 10px;">{{ $item->product?->hsn_code ?? '85171300' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format(($item->quantity * $item->unit_price) - $item->discount_amount, 2) }}</td>
                        <td class="text-center" style="font-size: 9.5px;">
                            @if($item->cgst > 0)
                                {{ $item->tax_rate / 2 }}%<br>(₹{{ number_format($item->cgst, 2) }})
                            @else - @endif
                        </td>
                        <td class="text-center" style="font-size: 9.5px;">
                            @if($item->sgst > 0)
                                {{ $item->tax_rate / 2 }}%<br>(₹{{ number_format($item->sgst, 2) }})
                            @else - @endif
                        </td>
                        <td class="text-right font-bold">{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Calculations Summary -->
        <table>
            <tr>
                <td style="width: 60%; vertical-align: top;" class="p-2">
                    <div style="font-size: 10px; margin-bottom: 8px;">
                        <strong>Payment Breakdown:</strong><br>
                        Payment Method: <span class="uppercase font-bold">{{ $sale->payment_method }}</span> | Amount Paid: ₹{{ number_format($sale->paid_amount, 2) }}
                        @if($sale->due_amount > 0)
                            | <span style="color: #dc2626; font-weight: bold;">Outstanding: ₹{{ number_format($sale->due_amount, 2) }}</span>
                        @endif
                    </div>
                    <div style="font-size: 9px; color: #475569; border-top: 1px solid #e2e8f0; padding-top: 6px;">
                        <strong>Bank Transfer Details for Direct Payment:</strong><br>
                        Bank: HDFC Bank Ltd | A/C No: 50200088991122 | IFSC: HDFC0000128<br>
                        UPI ID: webotappretail@hdfcbank
                    </div>
                </td>
                <td style="width: 40%; vertical-align: top;" class="p-2 bg-gray">
                    <table style="border: none;">
                        <tr style="border: none;"><td style="border: none; padding: 2px 0;">Total Taxable Value:</td><td style="border: none; padding: 2px 0;" class="text-right">₹{{ number_format($sale->subtotal - $sale->discount_amount, 2) }}</td></tr>
                        @if($sale->cgst > 0)
                            <tr style="border: none;"><td style="border: none; padding: 2px 0;">Total CGST:</td><td style="border: none; padding: 2px 0;" class="text-right">₹{{ number_format($sale->cgst, 2) }}</td></tr>
                        @endif
                        @if($sale->sgst > 0)
                            <tr style="border: none;"><td style="border: none; padding: 2px 0;">Total SGST:</td><td style="border: none; padding: 2px 0;" class="text-right">₹{{ number_format($sale->sgst, 2) }}</td></tr>
                        @endif
                        @if($sale->igst > 0)
                            <tr style="border: none;"><td style="border: none; padding: 2px 0;">Total IGST:</td><td style="border: none; padding: 2px 0;" class="text-right">₹{{ number_format($sale->igst, 2) }}</td></tr>
                        @endif
                        @if($sale->exchange_allowance > 0)
                            <tr style="border: none;"><td style="border: none; padding: 2px 0; color: #16a34a; font-weight: bold;">Trade-in Allowance:</td><td style="border: none; padding: 2px 0; color: #16a34a; font-weight: bold;" class="text-right">-₹{{ number_format($sale->exchange_allowance, 2) }}</td></tr>
                        @endif
                        <tr style="border-top: 1px solid #cbd5e1;"><td style="border: none; padding: 4px 0; font-size: 13px; font-weight: bold;">Invoice Total:</td><td style="border: none; padding: 4px 0; font-size: 13px; font-weight: bold; color: #4f46e5;" class="text-right">₹{{ number_format($sale->grand_total, 2) }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Signatures & Terms Footer -->
        <table>
            <tr>
                <td style="width: 65%; vertical-align: top; font-size: 9px;" class="p-2">
                    <strong>Terms & Conditions:</strong><br>
                    1. Goods once sold cannot be taken back unless approved under store exchange policy.<br>
                    2. Warranty for all smartphones and serialized electronics is provided directly by respective manufacturer authorized service centers.<br>
                    3. Retain this invoice copy and the device original box with matching IMEI for all manufacturer warranty claims.<br>
                    4. Disputes subject to Mumbai jurisdiction only.
                </td>
                <td style="width: 35%; text-align: center; vertical-align: bottom; height: 75px;" class="p-2">
                    <div style="font-size: 9px; margin-bottom: 35px;">For {{ $company->legal_name ?? 'Webotapp Retail India Pvt Ltd' }}</div>
                    <div class="font-bold" style="border-top: 1px solid #000; padding-top: 2px; font-size: 10px;">Authorized Signatory</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
