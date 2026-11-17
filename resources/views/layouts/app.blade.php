<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title')</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="pantalla" style="gap: @yield('gap', '18px')">
@yield('content')
<nav style="margin-top: auto; height: 76px; display: flex; justify-content: space-around; align-items: center; border-top: 1px solid #D6D3CC; font-size: 14px">
<a href="{{ route('jugar') }}" style="text-decoration: none; {{ request()->routeIs('jugar') ? 'font-weight: 700; color: #2563EB' : 'color: inherit' }}">Jugar</a>
<a href="{{ route('historial') }}" style="text-decoration: none; {{ request()->routeIs('historial') ? 'font-weight: 700; color: #2563EB' : 'color: inherit' }}">Historial</a>
<span>Estadísticas</span>
</nav>
</div>
</body>
</html>
