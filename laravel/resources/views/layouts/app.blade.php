<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <style>
        .pair-btc  > td { background-color: #d1f2d6 !important; }
        .pair-eth  > td { background-color: #fff3bf !important; }
        .pair-usdt > td { background-color: #cfe2ff !important; }
        .legend span { display: inline-block; padding: .15rem .5rem; border-radius: .25rem; margin-right: .5rem; }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand bg-white border-bottom mb-4">
    <div class="container">
        <span class="navbar-brand">{{ config('app.name') }}</span>
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link @if(request()->routeIs('courses.index')) active @endif" href="{{ route('courses.index') }}">База данных</a></li>
            <li class="nav-item"><a class="nav-link @if(request()->routeIs('courses.json')) active @endif" href="{{ route('courses.json') }}">JSON-файл</a></li>
            <li class="nav-item"><a class="nav-link @if(request()->routeIs('binance.index')) active @endif" href="{{ route('binance.index') }}">Binance API</a></li>
        </ul>
    </div>
</nav>
<main class="container pb-5">
    <h1 class="h3 mb-3">@yield('title')</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @yield('content')
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
    $('table.datatable').DataTable({
        pageLength: 25,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/ru.json' },
    });
</script>
</body>
</html>
