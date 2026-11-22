<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Expense;
use App\Models\JournalEntry;
use App\Models\Purchase;
use App\Models\Sale;
use Exception;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Seed or get default Chart of Accounts for a tenant.
     */
    public function getOrCreateAccounts(int $tenantId, ?int $branchId = null): array
    {
        $defaultAccounts = [
            ['code' => '1001', 'name' => 'Cash in Hand', 'type' => 'asset', 'subtype' => 'cash'],
            ['code' => '1002', 'name' => 'Bank Account', 'type' => 'asset', 'subtype' => 'bank'],
            ['code' => '1003', 'name' => 'Accounts Receivable (Customers)', 'type' => 'asset', 'subtype' => 'receivables'],
            ['code' => '1004', 'name' => 'Inventory Asset', 'type' => 'asset', 'subtype' => 'inventory'],
            ['code' => '2001', 'name' => 'Accounts Payable (Suppliers)', 'type' => 'liability', 'subtype' => 'payables'],
            ['code' => '2002', 'name' => 'GST Output Tax Payable (CGST)', 'type' => 'liability', 'subtype' => 'gst'],
            ['code' => '2003', 'name' => 'GST Output Tax Payable (SGST)', 'type' => 'liability', 'subtype' => 'gst'],
            ['code' => '2004', 'name' => 'GST Output Tax Payable (IGST)', 'type' => 'liability', 'subtype' => 'gst'],
            ['code' => '3001', 'name' => 'Owner Capital', 'type' => 'equity', 'subtype' => 'capital'],
            ['code' => '4001', 'name' => 'Smartphone Sales Revenue', 'type' => 'revenue', 'subtype' => 'sales'],
            ['code' => '4002', 'name' => 'Accessories Sales Revenue', 'type' => 'revenue', 'subtype' => 'sales'],
            ['code' => '4003', 'name' => 'Repair Service Revenue', 'type' => 'revenue', 'subtype' => 'service'],
            ['code' => '5001', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'subtype' => 'cogs'],
            ['code' => '5002', 'name' => 'Store Operating Expenses', 'type' => 'expense', 'subtype' => 'operating'],
        ];

        $accounts = [];
        foreach ($defaultAccounts as $acc) {
            $record = Account::withoutGlobalScope('tenant')->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'code' => $acc['code'],
                ],
                [
                    'branch_id' => $branchId,
                    'name' => $acc['name'],
                    'type' => $acc['type'],
                    'subtype' => $acc['subtype'],
                    'balance' => 0,
                    'is_active' => true,
                ]
            );
            $accounts[$acc['code']] = $record;
        }

        return $accounts;
    }

    /**
     * Post a balanced journal entry with audit lines.
     */
    public function postJournalEntry(
        int $tenantId,
        int $branchId,
        string $referenceType,
        int $referenceId,
        string $narration,
        array $lines, // array of ['account_id' => x, 'debit' => y, 'credit' => z, 'description' => '']
        ?int $userId = null
    ): JournalEntry {
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($lines as $line) {
            $totalDebit += (float) ($line['debit'] ?? 0);
            $totalCredit += (float) ($line['credit'] ?? 0);
        }

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new Exception("Journal entry unbalanced! Total Debits (" . round($totalDebit, 2) . ") != Total Credits (" . round($totalCredit, 2) . ")");
        }

        return DB::transaction(function () use ($tenantId, $branchId, $referenceType, $referenceId, $narration, $lines, $userId) {
            $entryNumber = 'JE-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $entry = JournalEntry::withoutGlobalScope('tenant')->create([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'entry_number' => $entryNumber,
                'entry_date' => now()->toDateString(),
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'narration' => $narration,
                'created_by' => $userId ?? auth()->id(),
            ]);

            foreach ($lines as $line) {
                $debit = (float) ($line['debit'] ?? 0);
                $credit = (float) ($line['credit'] ?? 0);

                $entry->lines()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $debit,
                    'credit' => $credit,
                    'description' => $line['description'] ?? null,
                ]);

                // Update account balance
                $account = Account::withoutGlobalScope('tenant')->find($line['account_id']);
                if ($account) {
                    // For Asset & Expense: Debit increases (+), Credit decreases (-)
                    // For Liability, Equity, Revenue: Credit increases (+), Debit decreases (-)
                    if (in_array($account->type, ['asset', 'expense'])) {
                        $account->balance += ($debit - $credit);
                    } else {
                        $account->balance += ($credit - $debit);
                    }
                    $account->save();
                }
            }

            return $entry;
        });
    }

    /**
     * Post journal entry for a Sale.
     */
    public function recordSale(Sale $sale): void
    {
        $accounts = $this->getOrCreateAccounts($sale->tenant_id, $sale->branch_id);

        $lines = [];

        // 1. Payment received (Debit Cash / Bank / Receivable)
        if ($sale->paid_amount > 0) {
            $isBankOrUpi = in_array(strtolower($sale->payment_method), ['upi', 'card', 'bank_transfer', 'emi']);
            $paymentAcc = $isBankOrUpi ? $accounts['1002'] : $accounts['1001'];

            $lines[] = [
                'account_id' => $paymentAcc->id,
                'debit' => $sale->paid_amount,
                'credit' => 0,
                'description' => "Payment received for Sale #{$sale->invoice_number} ({$sale->payment_method})",
            ];
        }

        // 2. Customer Due (Accounts Receivable)
        if ($sale->due_amount > 0) {
            $lines[] = [
                'account_id' => $accounts['1003']->id,
                'debit' => $sale->due_amount,
                'credit' => 0,
                'description' => "Customer dues for Sale #{$sale->invoice_number}",
            ];
        }

        // 2b. Exchange Allowance (Debit Used Device / Inventory Asset)
        if ($sale->exchange_allowance > 0) {
            $lines[] = [
                'account_id' => $accounts['1004']->id,
                'debit' => $sale->exchange_allowance,
                'credit' => 0,
                'description' => "Trade-in device allowance on Sale #{$sale->invoice_number}",
            ];
        }

        // 3. Sales Revenue (Credit)
        $revenueAmount = $sale->subtotal - $sale->discount_amount;
        $lines[] = [
            'account_id' => $accounts['4001']->id,
            'debit' => 0,
            'credit' => $revenueAmount,
            'description' => "Sales revenue for Sale #{$sale->invoice_number}",
        ];

        // 4. GST Output Tax (Credit)
        if ($sale->cgst > 0) {
            $lines[] = [
                'account_id' => $accounts['2002']->id,
                'debit' => 0,
                'credit' => $sale->cgst,
                'description' => "CGST on Sale #{$sale->invoice_number}",
            ];
        }
        if ($sale->sgst > 0) {
            $lines[] = [
                'account_id' => $accounts['2003']->id,
                'debit' => 0,
                'credit' => $sale->sgst,
                'description' => "SGST on Sale #{$sale->invoice_number}",
            ];
        }
        if ($sale->igst > 0) {
            $lines[] = [
                'account_id' => $accounts['2004']->id,
                'debit' => 0,
                'credit' => $sale->igst,
                'description' => "IGST on Sale #{$sale->invoice_number}",
            ];
        }

        $this->postJournalEntry(
            $sale->tenant_id,
            $sale->branch_id,
            'Sale',
            $sale->id,
            "Posting for POS Sale #{$sale->invoice_number}",
            $lines,
            $sale->created_by
        );
    }
}
