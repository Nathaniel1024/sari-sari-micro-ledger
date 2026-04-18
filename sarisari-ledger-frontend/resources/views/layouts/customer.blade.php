<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Debt</title>
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
    <main class="app-main-sm">
        <h1>My Debt Ledger</h1>
        <p class="muted">View your current balance and latest transactions.</p>

        <form class="m-0" method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn-ghost" type="submit">Logout</button>
        </form>

        @yield('content')
        {{ $slot ?? '' }}
    </main>

    @livewireScripts
</body>

</html>
