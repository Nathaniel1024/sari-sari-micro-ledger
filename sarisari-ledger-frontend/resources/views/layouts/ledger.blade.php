<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sari-Sari Micro-Ledger')</title>
    <script>
        window.StellarConfig = {{ \Illuminate\Support\Js::from([
            'horizonUrl' => config('services.stellar.horizon_url'),
            'networkPassphrase' => config('services.stellar.network_passphrase'),
            'storePublicKey' => config('services.stellar.store_public_key'),
        ]) }};
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body>
    <main class="app-main">
        <h1>Sari-Sari Micro-Ledger</h1>
        <p class="subtitle">Track Utang using contract-style operations: <strong>add_credit</strong>,
            <strong>pay_credit</strong>, and <strong>get_debt</strong>.
        </p>

        <nav class="top-nav">
            <a class="top-nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="top-nav-link" href="{{ route('admin.customers.index') }}">Customers</a>
            <a class="top-nav-link" href="{{ route('admin.credits.create') }}">Add Credit</a>
            <a class="top-nav-link" href="{{ route('admin.payments.create') }}">Record Payment</a>
            <a class="top-nav-link" href="{{ route('admin.debt.form') }}">Check Debt</a>
            <a class="top-nav-link" href="{{ route('admin.debtors') }}">Debt Search</a>
            <span class="spacer"></span>
            <form class="m-0" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-ghost">Logout</button>
            </form>
        </nav>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
        {{ $slot ?? '' }}
    </main>
    @livewireScripts
</body>

</html>
