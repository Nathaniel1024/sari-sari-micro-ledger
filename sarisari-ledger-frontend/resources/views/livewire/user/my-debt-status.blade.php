<section class="card" wire:poll.5s>
    <h2>My Debt Status</h2>
    <p class="muted">This section updates automatically every 5 seconds.</p>

    @if (! $customer)
        <p class="muted">Your account is not linked to a debt record yet. Please ask Aling Maria to link your account.</p>
    @else
        <p><strong>Name:</strong> {{ $customer->name }}</p>
        <p><strong>Code:</strong> {{ $customer->code }}</p>
        <p><strong>Current Debt:</strong> {{ number_format($currentDebt, 0) }} PHP</p>

        @if ($currentDebt > 0)
            <form class="card" method="POST" action="{{ route('customer.payments.store') }}" data-wallet-flow="customer-payment">
                @csrf
                <h3>Pay Using Freighter</h3>
                <label for="wallet_payment_amount">Amount (PHP)</label>
                <input id="wallet_payment_amount" name="amount" type="number" min="1" max="{{ $currentDebt }}"
                    placeholder="Enter amount" required>
                <input type="hidden" name="wallet_tx_hash" value="">
                <p class="muted">A Freighter payment transaction will be signed and submitted before this is saved.</p>
                <button type="submit">Pay And Record</button>
            </form>
        @endif

        <h3>Recent Transactions</h3>
        @if ($recentEntries->isEmpty())
            <p class="muted">No transactions yet.</p>
        @else
            <div class="table-wrap table-wrap-sm">
                <table>
                    <tr>
                        <th>Time</th>
                        <th>Action</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                    </tr>
                    @foreach ($recentEntries as $entry)
                        <tr>
                            <td>{{ optional($entry->occurred_at)->toDateTimeString() ?? 'N/A' }}</td>
                            <td>{{ $entry->type }}</td>
                            <td>{{ number_format($entry->amount, 0) }} PHP</td>
                            <td>{{ number_format($entry->balance_after, 0) }} PHP</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
    @endif
</section>
