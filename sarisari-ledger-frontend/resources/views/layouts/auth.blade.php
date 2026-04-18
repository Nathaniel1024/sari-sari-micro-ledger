<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sari-Sari Micro-Ledger')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="auth-shell">
    <main class="auth-card">
        @if ($errors->any())
            <div class="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>
</body>

</html>
