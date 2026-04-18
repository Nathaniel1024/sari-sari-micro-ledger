@extends('layouts.ledger')

@section('title', 'Add Credit')

@section('content')
    <section class="card">
        <h2>Add Credit</h2>
        <p class="muted">Use this for the `add_credit` flow.</p>
        @if ($customers->isEmpty())
            <p class="muted">No customers yet. Add one from the <a href="{{ route('admin.customers.index') }}">Customers</a>
                page first.</p>
        @else
        <form method="POST" action="{{ route('admin.credits.store') }}" data-wallet-flow="admin-credit">
            @csrf
            <label for="customer_id">Customer</label>
            <select id="customer_id" name="customer_id" required>
                <option value="">Select customer</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" @selected((string) old('customer_id') === (string) $customer->id)>
                        {{ $customer->name }} ({{ $customer->code }})
                    </option>
                @endforeach
            </select>

            <label for="credit_amount">Amount (PHP)</label>
            <input id="credit_amount" name="amount" type="number" min="1" placeholder="100" required>
            <input type="hidden" name="wallet_tx_hash" value="">
            <p class="muted">Freighter will send XLM from your connected wallet before this is saved.</p>

            <button type="submit">Submit add_credit</button>
        </form>
        @endif
    </section>
@endsection
