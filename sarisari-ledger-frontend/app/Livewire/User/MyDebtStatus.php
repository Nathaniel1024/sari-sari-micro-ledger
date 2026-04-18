<?php

namespace App\Livewire\User;

use App\Models\Customer;
use App\Models\LedgerEntry;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.customer')]
class MyDebtStatus extends Component
{
    public function render()
    {
        $authUserId = auth()->id();

        $customer = Customer::query()
            ->where('auth_user_id', $authUserId)
            ->first();

        if (! $customer) {
            return view('livewire.user.my-debt-status', [
                'customer' => null,
                'currentDebt' => 0,
                'recentEntries' => collect(),
            ]);
        }

        $recentEntries = LedgerEntry::query()
            ->where('customer_id', $customer->id)
            ->orderByDesc('occurred_at')
            ->limit(10)
            ->get();

        $currentDebt = (int) (optional($recentEntries->first())->balance_after ?? 0);

        return view('livewire.user.my-debt-status', [
            'customer' => $customer,
            'currentDebt' => $currentDebt,
            'recentEntries' => $recentEntries,
        ]);
    }
}
