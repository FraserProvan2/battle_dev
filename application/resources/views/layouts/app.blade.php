<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>battle_dev</title>
    <script src="{{ mix('js/app.js') }}" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <main class="py-3 container">

            @include('layouts.includes.navbar')

            <div class="card rounded-0">
                <div class="card-body">
                    @yield('content')
                </div>
            </div>

        </main>
    </div>

    <script src="https://kit.fontawesome.com/beff54eb4d.js" crossorigin="anonymous"></script>
</body>
</html>
