@extends('layouts.ledger')

@section('title', 'Record Payment')

@section('content')
    <section class="card">
        <h2>Record Payment</h2>
        <p class="muted">Use this for the `pay_credit` flow.</p>
        @if ($customers->isEmpty())
            <p class="muted">No customers yet. Add one from the <a href="{{ route('admin.customers.index') }}">Customers</a>
                page first.</p>
        @else
        <form method="POST" action="{{ route('admin.payments.store') }}" data-wallet-flow="admin-payment">
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

            <label for="payment_amount">Payment (PHP)</label>
            <input id="payment_amount" name="payment" type="number" min="1" placeholder="40" required>
            <input type="hidden" name="wallet_tx_hash" value="">
            <p class="muted">Freighter will send XLM using the payment amount before this is saved.</p>

            <button type="submit">Submit pay_credit</button>
        </form>
        @endif
    </section>
@endsection
