@extends('layouts.ledger')

@section('title', 'Check Debt')

@section('content')
    <section class="card">
        <h2>Check Debt</h2>
        <p class="muted">Use this for the `get_debt` flow.</p>
        @if ($customers->isEmpty())
            <p class="muted">No customers yet. Add one from the <a href="{{ route('admin.customers.index') }}">Customers</a>
                page first.</p>
        @else
        <form method="POST" action="{{ route('admin.debt.check') }}">
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

            <button type="submit">Run get_debt</button>
        </form>
        @endif
    </section>

    @if (!is_null($checkedCustomer) && !is_null($checkedDebt))
        <section class="card">
            <h2>Debt Result</h2>
            <p>Current debt for <strong>{{ $checkedCustomer }}</strong>: <strong>{{ number_format($checkedDebt, 0) }}
                    PHP</strong></p>
        </section>
    @endif
@endsection
