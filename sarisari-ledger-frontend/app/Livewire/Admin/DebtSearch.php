<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\LedgerEntry;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.ledger')]
class DebtSearch extends Component
{
    public string $search = '';

    public function render()
    {
        $adminId = auth()->id();
        $search = trim($this->search);

        $customers = Customer::query()
            ->with('authUser:id,email')
            ->where('user_id', $adminId)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search): void {
                    $builder
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . strtoupper($search) . '%')
                        ->orWhereHas('authUser', function ($userQuery) use ($search): void {
                            $userQuery->where('email', 'like', '%' . strtolower($search) . '%');
                        });
                });
            })
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'auth_user_id']);

        $latestBalances = LedgerEntry::query()
            ->selectRaw('customer_id, MAX(id) as latest_entry_id')
            ->where('user_id', $adminId)
            ->whereIn('customer_id', $customers->pluck('id'))
            ->groupBy('customer_id')
            ->pluck('latest_entry_id', 'customer_id');

        $entriesById = LedgerEntry::query()
            ->whereIn('id', $latestBalances->values())
            ->get(['id', 'customer_id', 'balance_after'])
            ->keyBy('id');

        $rows = $customers->map(function (Customer $customer) use ($latestBalances, $entriesById): array {
            $latestEntryId = $latestBalances[$customer->id] ?? null;
            $latestEntry = $latestEntryId ? $entriesById->get($latestEntryId) : null;

            return [
                'name' => $customer->name,
                'code' => $customer->code,
                'email' => $customer->authUser?->email ?? 'N/A',
                'debt' => (int) ($latestEntry->balance_after ?? 0),
            ];
        })->filter(fn (array $row): bool => $row['debt'] > 0)->values();

        return view('livewire.admin.debt-search', [
            'rows' => $rows,
        ]);
    }
}
