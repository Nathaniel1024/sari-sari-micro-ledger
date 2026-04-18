@extends('layouts.ledger')

@section('title', 'Ledger Dashboard')

@section('content')
    <livewire:admin.debt-search />

    <section class="card">
        <h2>Recent Transactions</h2>
        @if ($entries->isEmpty())
            <p class="muted">No transactions found yet.</p>
        @else
            <div class="table-wrap">
                <table>
                    <tr>
                        <th>Time</th>
                        <th>Action</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                    </tr>
                    @foreach ($entries as $entry)
                        <tr>
                            <td>{{ optional($entry->occurred_at)->toDateTimeString() ?? 'N/A' }}</td>
                            <td>{{ $entry->type }}</td>
                            <td>{{ $entry->customer?->name }} ({{ $entry->customer?->code }})</td>
                            <td>{{ number_format($entry->amount, 0) }} PHP</td>
                            <td>{{ number_format($entry->balance_after, 0) }} PHP</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
    </section>
@endsection
