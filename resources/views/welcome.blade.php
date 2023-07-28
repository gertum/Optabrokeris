<!DOCTYPE html>
{{-- Panašu, kad šitas failas apskritai niekur nenaudojamas --}}

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Inkodus solver</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    <!-- Styles -->
</head>
<body class="antialiased">
<h1>Inkodus solver</h1>

<ul>
    <li><a href="/login">Login</a></li>
    <li><a href="/logout">Logout</a></li>
    <li><a href="/profile">Profile</a></li>
    <li><a href="/dashboard">Dashboard</a></li>
</ul>

</body>
</html>
