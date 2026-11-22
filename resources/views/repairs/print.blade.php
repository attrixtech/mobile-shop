<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Card #{{ $job->job_number }}</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.35; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border { border: 1px solid #cbd5e1; }
        .p-2 { padding: 8px; }
        .bg-gray { background-color: #f8fafc; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; }
        th { background-color: #f1f5f9; font-size: 10px; font-weight: bold; text-align: left; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 15px; background: #4f46e5; color: white; border: none; font-weight: bold; cursor: pointer; border-radius: 4px;">Print Job Card</button>
        <button onclick="window.close()" style="padding: 6px 10px; background: #e2e8f0; border: none; cursor: pointer; border-radius: 4px; margin-left: 5px;">Close</button>
    </div>

    <div class="border">
        <!-- Header -->
        <div class="p-2 border" style="border-bottom: 1px solid #cbd5e1; text-align: center; background: #f8fafc;">
            <h2 style="margin: 0; font-size: 16px; text-transform: uppercase;">DEVICE REPAIR JOB CARD</h2>
            <div style="font-size: 12px; font-weight: bold; margin-top: 3px;">{{ $job->branch?->company?->name ?? 'Webotapp Mobile Repair Center' }}</div>
            <div style="font-size: 10px; color: #64748b;">{{ $job->branch?->address }} | Phone: {{ $job->branch?->phone }}</div>
        </div>

        <table>
            <tr>
                <td style="width: 50%;">
                    <div><strong>Job Ticket #:</strong> <span style="font-family: monospace; font-size: 13px; font-weight: bold; color: #4f46e5;">{{ $job->job_number }}</span></div>
                    <div><strong>Date Received:</strong> {{ $job->received_at->format('d-M-Y H:i') }}</div>
                    <div><strong>Est. Delivery:</strong> {{ $job->estimated_delivery_at ? $job->estimated_delivery_at->format('d-M-Y') : 'To be confirmed' }}</div>
                    <div><strong>Current Status:</strong> <span style="text-transform: uppercase; font-weight: bold;">{{ str_replace('_', ' ', $job->status) }}</span></div>
                </td>
                <td style="width: 50%;">
                    <div><strong>Customer Name:</strong> {{ $job->customer?->name }}</div>
                    <div><strong>Mobile Number:</strong> {{ $job->customer?->phone }}</div>
                    <div><strong>Address:</strong> {{ $job->customer?->city }}</div>
                    <div><strong>Assigned Tech:</strong> {{ $job->technician?->name ?: 'Store Technician' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; margin-bottom: 3px;">Device Diagnosis & Intake Details:</div>
                    <div><strong>Brand & Model:</strong> {{ $job->brand }} {{ $job->model }}</div>
                    <div><strong>IMEI / Serial:</strong> {{ $job->imei ?: 'Not provided' }}</div>
                    <div><strong>Reported Problem:</strong> {{ $job->reported_problem }}</div>
                    <div><strong>Physical Condition at Reception:</strong> {{ $job->device_condition ?: 'Normal wear' }}</div>
                    <div><strong>Accessories Received:</strong> {{ $job->accessories_received ?: 'None' }}</div>
                    <div><strong>Warranty Category:</strong> {{ ucwords(str_replace('_', ' ', $job->warranty_status)) }}</div>
                </td>
            </tr>
        </table>

        <!-- Charges -->
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Description / Spare Part</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th style="text-align: right;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($job->items as $item)
                    <tr>
                        <td style="text-transform: uppercase;">{{ $item->type }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary & Balance -->
        <table>
            <tr>
                <td style="width: 60%; vertical-align: top; font-size: 9px;">
                    <strong>Service Terms:</strong><br>
                    1. We are not responsible for any customer data loss during software or hardware repair. Customers are requested to back up data.<br>
                    2. Devices not collected within 45 days after readiness will be subject to storage/salvage disposal.<br>
                    3. 30 Days service warranty applies strictly on the replaced spare parts only.
                </td>
                <td style="width: 40%; vertical-align: top;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                        <span>Final Repair Bill:</span>
                        <strong>₹{{ number_format($job->final_cost, 2) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                        <span>Advance Paid:</span>
                        <strong style="color: #16a34a;">₹{{ number_format($job->advance_paid, 2) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #cbd5e1; padding-top: 4px; font-size: 13px;">
                        <span>Balance Due:</span>
                        <strong style="color: #dc2626;">₹{{ number_format(max(0, $job->final_cost - $job->advance_paid), 2) }}</strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="height: 50px; vertical-align: bottom; text-align: center;">
                    <div style="border-top: 1px solid #94a3b8; width: 60%; margin: 0 auto; font-size: 9px; padding-top: 2px;">
                        Customer Acknowledgment Signature
                    </div>
                </td>
                <td style="height: 50px; vertical-align: bottom; text-align: center;">
                    <div style="border-top: 1px solid #94a3b8; width: 60%; margin: 0 auto; font-size: 9px; padding-top: 2px;">
                        Store Service Desk Stamp & Sign
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
