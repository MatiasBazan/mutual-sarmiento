<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="/logo.jpg">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #09090b;
            color: #fafafa;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 14px;
            min-height: 100vh;
        }
        a { color: inherit; text-decoration: none; }
        input, select, button, textarea { font-family: inherit; }
        [x-cloak] { display: none !important; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<nav style="background:#09090b; border-bottom:1px solid #27272a; height:56px; display:flex; align-items:center; padding:0 32px; gap:32px; position:sticky; top:0; z-index:40;">

    <!-- Logo -->
    <a href="/">
        <img src="{{ asset('logo.jpg') }}" alt="{{ config('app.name') }}"
             style="height:32px; width:auto; border-radius:6px; object-fit:cover;">
    </a>

    @auth
    <!-- Nav links -->
    <div style="display:flex; gap:2px; flex:1;">
        <a href="{{ route('resumenes.index') }}"
           style="padding:6px 12px; border-radius:6px; font-size:13px; transition:all 0.15s;
                  color:{{ request()->routeIs('resumenes.*') ? '#fafafa' : '#a1a1aa' }};
                  background:{{ request()->routeIs('resumenes.*') ? '#27272a' : 'transparent' }};">
            Resúmenes
        </a>
        <a href="{{ route('clientes.index') }}"
           style="padding:6px 12px; border-radius:6px; font-size:13px; transition:all 0.15s;
                  color:{{ request()->routeIs('clientes.*') ? '#fafafa' : '#a1a1aa' }};
                  background:{{ request()->routeIs('clientes.*') ? '#27272a' : 'transparent' }};">
            Clientes
        </a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('usuarios.index') }}"
           style="padding:6px 12px; border-radius:6px; font-size:13px; transition:all 0.15s;
                  color:{{ request()->routeIs('usuarios.*') ? '#fafafa' : '#a1a1aa' }};
                  background:{{ request()->routeIs('usuarios.*') ? '#27272a' : 'transparent' }};">
            Usuarios
        </a>
        @endif
        <a href="{{ route('turnero.panel') }}"
           style="padding:6px 12px; border-radius:6px; font-size:13px; transition:all 0.15s;
                  color:{{ request()->routeIs('turnero.panel') ? '#fafafa' : '#a1a1aa' }};
                  background:{{ request()->routeIs('turnero.panel') ? '#27272a' : 'transparent' }};">
            Mi Box
        </a>
        <a href="{{ route('turnero.tv') }}" target="_blank"
           style="padding:6px 12px; border-radius:6px; font-size:13px; color:#a1a1aa;
                  display:flex; align-items:center; gap:6px; transition:all 0.15s;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="3" width="20" height="14" rx="2"/>
                <path d="M8 21h8M12 17v4"/>
            </svg>
            Pantalla TV
        </a>
    </div>

    <!-- Usuario + salir -->
    <div style="display:flex; align-items:center; gap:16px;">
        <span style="font-size:13px; color:#a1a1aa;">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    style="background:transparent; border:1px solid #3f3f46; color:#a1a1aa;
                           padding:5px 12px; border-radius:6px; font-size:12px; cursor:pointer;">
                Salir
            </button>
        </form>
    </div>
    @endauth
</nav>

@if(session('success'))
<div style="max-width:1280px; margin:16px auto 0; padding:0 32px;">
    <div style="display:flex; align-items:center; gap:8px; background:rgba(34,197,94,0.08);
                border:1px solid rgba(34,197,94,0.2); color:#22c55e;
                border-radius:8px; padding:10px 16px; font-size:13px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
</div>
@endif

@if(session('error'))
<div style="max-width:1280px; margin:16px auto 0; padding:0 32px;">
    <div style="display:flex; align-items:center; gap:8px; background:rgba(239,68,68,0.08);
                border:1px solid rgba(239,68,68,0.2); color:#ef4444;
                border-radius:8px; padding:10px 16px; font-size:13px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ session('error') }}
    </div>
</div>
@endif

<main style="padding:32px; max-width:1280px; margin:0 auto;">
    {{ $slot }}
</main>

</body>
</html>
