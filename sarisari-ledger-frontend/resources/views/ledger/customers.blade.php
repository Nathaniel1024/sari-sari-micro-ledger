@extends('layouts.ledger')

@section('title', 'Customers')

@section('content')
    <section class="card">
        <h2>Register Customer</h2>
        <p class="muted">Create a debtor account and customer record together.</p>
        <form method="POST" action="{{ route('admin.customers.store') }}">
            @csrf
            <label for="name">Customer Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="e.g. John Dela Cruz" required>
            <label for="email">Login Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="john@example.com" required>
            <label for="password">Temporary Password</label>
            <input id="password" name="password" type="password" minlength="8" required>
            {{-- Stellar public key is temporarily disabled. --}}
            {{--
            <label for="stellar_public_key">Stellar Public Key</label>
            <input id="stellar_public_key" name="stellar_public_key" type="text" value="{{ old('stellar_public_key') }}"
                placeholder="G..." required>
            --}}
            <button type="submit">Add Customer</button>
        </form>
    </section>

    <section class="card">
        <h2>Customer List</h2>
        @if ($customers->isEmpty())
            <p class="muted">No customers yet.</p>
        @else
            <div class="table-wrap">
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Customer Code</th>
                        <th>Linked Account</th>
                        {{-- <th>Wallet</th> --}}
                    </tr>
                    @foreach ($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->code }}</td>
                            <td>{{ $customer->authUser?->email ?? 'N/A' }}</td>
                            {{-- <td>{{ $customer->stellar_public_key ?? 'N/A' }}</td> --}}
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
    </section>
@endsection
