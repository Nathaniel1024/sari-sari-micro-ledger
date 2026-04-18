<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LedgerEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LedgerController extends Controller
{
    public function index()
    {
        return view('ledger.index', $this->buildLedgerViewData());
    }

    public function createCredit()
    {
        return view('ledger.credit', $this->buildLedgerViewData());
    }

    public function createPayment()
    {
        return view('ledger.payment', $this->buildLedgerViewData());
    }

    public function showCustomers()
    {
        return view('ledger.customers', $this->buildLedgerViewData());
    }

    public function showDebtForm()
    {
        return view('ledger.debt', $this->buildLedgerViewData());
    }

    public function storeCustomer(Request $request)
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            // 'stellar_public_key' is temporarily disabled.
        ]);

        $user = $request->user();
        $name = trim($payload['name']);
        $canonicalName = $this->canonicalizeName($name);
        $code = $this->generateUniqueCustomerCode($user->id, $name);

        $alreadyExists = Customer::query()
            ->where('user_id', $user->id)
            ->where('canonical_name', $canonicalName)
            ->exists();

        if ($alreadyExists) {
            throw ValidationException::withMessages([
                'name' => 'Customer already exists. Use the existing customer record.',
            ]);
        }

        DB::transaction(function () use ($payload, $user, $name, $canonicalName, $code): void {
            $authUser = User::query()->create([
                'name' => $name,
                'email' => strtolower($payload['email']),
                'password' => Hash::make($payload['password']),
                'role' => 'customer',
            ]);

            Customer::query()->create([
                'user_id' => $user->id,
                'auth_user_id' => $authUser->id,
                'name' => $name,
                'canonical_name' => $canonicalName,
                'code' => $code,
                // 'stellar_public_key' => strtoupper($payload['stellar_public_key']),
            ]);
        });

        return redirect()
            ->route('admin.customers.index')
            ->with('success', "Customer {$name} created.");
    }

    public function storeCredit(Request $request)
    {
        $payload = $request->validate([
            'customer_id' => ['required', 'integer'],
            'amount' => ['required', 'integer', 'min:1'],
            'wallet_tx_hash' => ['required', 'string', 'max:80', 'unique:ledger_entries,wallet_tx_hash'],
        ]);

        $user = $request->user();
        $customer = $this->resolveCustomer($user->id, (int) $payload['customer_id']);
        $amount = (int) $payload['amount'];
        $currentDebt = $this->currentDebtFor($user->id, $customer->id);
        $newDebt = $currentDebt + $amount;

        LedgerEntry::query()->create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'type' => 'add_credit',
            'amount' => $amount,
            'balance_after' => $newDebt,
            'wallet_tx_hash' => $payload['wallet_tx_hash'],
            'occurred_at' => now(),
        ]);

        return redirect()
            ->route('admin.credits.create')
            ->with('success', "Credit of {$this->formatAmount($amount)} added for {$customer->name}.");
    }

    public function storePayment(Request $request)
    {
        $payload = $request->validate([
            'customer_id' => ['required', 'integer'],
            'payment' => ['required', 'integer', 'min:1'],
            'wallet_tx_hash' => ['required', 'string', 'max:80', 'unique:ledger_entries,wallet_tx_hash'],
        ]);

        $user = $request->user();
        $customer = $this->resolveCustomer($user->id, (int) $payload['customer_id']);
        $payment = (int) $payload['payment'];
        $currentDebt = $this->currentDebtFor($user->id, $customer->id);

        if ($payment > $currentDebt) {
            throw ValidationException::withMessages([
                'payment' => 'Payment exceeds current debt',
            ]);
        }

        $newDebt = $currentDebt - $payment;
        LedgerEntry::query()->create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'type' => 'pay_credit',
            'amount' => $payment,
            'balance_after' => $newDebt,
            'wallet_tx_hash' => $payload['wallet_tx_hash'],
            'occurred_at' => now(),
        ]);

        return redirect()
            ->route('admin.payments.create')
            ->with('success', "Payment of {$this->formatAmount($payment)} recorded for {$customer->name}.");
    }

    public function checkDebt(Request $request)
    {
        $payload = $request->validate([
            'customer_id' => ['required', 'integer'],
        ]);

        $user = $request->user();
        $customer = $this->resolveCustomer($user->id, (int) $payload['customer_id']);
        $debt = $this->currentDebtFor($user->id, $customer->id);

        return redirect()
            ->route('admin.debt.form')
            ->with('checked_customer', $customer->name . ' (' . $customer->code . ')')
            ->with('checked_debt', $debt);
    }

    public function storeCustomerPayment(Request $request)
    {
        $payload = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'wallet_tx_hash' => ['required', 'string', 'max:80', 'unique:ledger_entries,wallet_tx_hash'],
        ]);

        $authUser = $request->user();
        $customer = Customer::query()->where('auth_user_id', $authUser->id)->firstOrFail();
        $currentDebt = $this->currentDebtFor($customer->user_id, $customer->id);
        $payment = (int) $payload['amount'];

        if ($payment > $currentDebt) {
            throw ValidationException::withMessages([
                'amount' => 'Payment exceeds current debt',
            ]);
        }

        $newDebt = $currentDebt - $payment;

        LedgerEntry::query()->create([
            'user_id' => $customer->user_id,
            'customer_id' => $customer->id,
            'type' => 'pay_credit',
            'amount' => $payment,
            'balance_after' => $newDebt,
            'wallet_tx_hash' => $payload['wallet_tx_hash'],
            'occurred_at' => now(),
        ]);

        return redirect()
            ->route('customer.debt')
            ->with('success', "Payment of {$this->formatAmount($payment)} submitted.");
    }

    private function buildLedgerViewData(): array
    {
        $user = auth()->user();
        $customers = Customer::query()
            ->with('authUser:id,email')
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'auth_user_id']);

        $entries = LedgerEntry::query()
            ->with('customer:id,name,code')
            ->where('user_id', $user->id)
            ->orderByDesc('occurred_at')
            ->limit(100)
            ->get();

        $debts = $customers->mapWithKeys(function (Customer $customer) use ($user) {
            $latestBalance = LedgerEntry::query()
                ->where('user_id', $user->id)
                ->where('customer_id', $customer->id)
                ->latest('occurred_at')
                ->value('balance_after');

            return [
                $customer->id => [
                    'name' => $customer->name,
                    'code' => $customer->code,
                    'debt' => (int) ($latestBalance ?? 0),
                ],
            ];
        })->all();

        return [
            'entries' => $entries,
            'debts' => $debts,
            'customers' => $customers,
            'checkedCustomer' => session('checked_customer'),
            'checkedDebt' => session('checked_debt'),
        ];
    }

    private function resolveCustomer(int $userId, int $customerId): Customer
    {
        return Customer::query()
            ->where('user_id', $userId)
            ->where('id', $customerId)
            ->firstOrFail();
    }

    private function currentDebtFor(int $userId, int $customerId): int
    {
        $balance = LedgerEntry::query()
            ->where('user_id', $userId)
            ->where('customer_id', $customerId)
            ->latest('occurred_at')
            ->value('balance_after');

        return (int) ($balance ?? 0);
    }

    private function canonicalizeName(string $name): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($name)) ?? '');
    }

    private function generateUniqueCustomerCode(int $userId, string $name): string
    {
        $base = strtoupper(preg_replace('/[^A-Z0-9]+/', '', $name) ?? '');
        $base = substr($base, 0, 8);

        if ($base === '') {
            $base = 'CUSTOMER';
        }

        for ($attempt = 0; $attempt < 20; $attempt++) {
            $code = $base . '-' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $exists = Customer::query()
                ->where('user_id', $userId)
                ->where('code', $code)
                ->exists();

            if (! $exists) {
                return $code;
            }
        }

        return $base . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    private function formatAmount(int $amount): string
    {
        return number_format($amount, 0) . ' PHP';
    }
}
