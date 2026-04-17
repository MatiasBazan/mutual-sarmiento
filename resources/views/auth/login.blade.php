<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ingresar — {{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="/logo.jpg">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: #0a0a0a;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(80,80,80,0.3), transparent);
        }
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="flex flex-col items-center mb-8">
            <img src="/logo.jpg" alt="{{ config('app.name') }}"
                 class="w-28 h-28 rounded-2xl object-cover shadow-2xl mb-5"
                 style="box-shadow: 0 0 60px rgba(255,255,255,0.08);">
            <h1 class="text-white text-xl font-bold tracking-tight">Mutual de Amigos</h1>
            <p class="text-gray-500 text-sm mt-1">Club Sarmiento</p>
        </div>

        {{-- Card --}}
        <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm p-8"
             style="background: rgba(255,255,255,0.04);">

            @if (session('status'))
                <div class="mb-5 text-xs text-green-400 bg-green-400/10 border border-green-400/20 rounded-lg px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-medium text-gray-400 mb-2">
                        Correo electrónico
                    </label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           placeholder="usuario@mutual.com"
                           class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-gray-400
                                  bg-white/10 border transition focus:outline-none
                                  {{ $errors->has('email')
                                      ? 'border-red-500/50 focus:border-red-500'
                                      : 'border-white/20 focus:border-white/50' }}">
                    @error('email')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-medium text-gray-400 mb-2">
                        Contraseña
                    </label>
                    <input id="password" type="password" name="password"
                           required autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-gray-400
                                  bg-white/10 border transition focus:outline-none
                                  {{ $errors->has('password')
                                      ? 'border-red-500/50 focus:border-red-500'
                                      : 'border-white/20 focus:border-white/50' }}">
                    @error('password')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Recordarme --}}
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember"
                               class="w-4 h-4 rounded border-gray-400 bg-gray-700 text-gray-900 focus:ring-0 focus:ring-offset-0 accent-white">
                        <span class="text-xs text-gray-300">Recordarme</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-xs text-gray-400 hover:text-white transition">
                            ¿Olvidaste la contraseña?
                        </a>
                    @endif
                </div>

                {{-- Botón --}}
                <button type="submit"
                        style="width:100%; margin-top:8px; padding:12px; background:#22c55e;
                               color:#000; font-weight:700; font-size:15px;
                               border:none; border-radius:8px; cursor:pointer;
                               transition:opacity 0.15s;"
                        onmouseover="this.style.opacity='0.9'"
                        onmouseout="this.style.opacity='1'">
                    Ingresar
                </button>

            </form>
        </div>

        <p class="text-center text-xs text-gray-700 mt-6">
            © {{ date('Y') }} Mutual de Amigos Club Sarmiento
        </p>
    </div>

</body>
</html>
