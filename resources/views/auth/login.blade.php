<!DOCTYPE html>
<html lang="id" data-theme="mrcatz-light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-x.png') }}">
    <title>Login - {{ config('site.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('components.ui.theme-switch-js')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans min-h-screen bg-brand-navy-deep flex items-center justify-center relative overflow-hidden">

    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.4&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-br from-brand-navy-deep via-brand-navy to-brand-navy-deep/80"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-brand-red/10 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-brand-red/10 rounded-full blur-3xl"></div>

    <div class="relative z-10 w-full max-w-md px-4">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center bg-white rounded-2xl px-6 py-3 mb-4 shadow-lg">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('site.name') }}" class="h-12 w-auto" />
            </div>
            <h1 class="text-2xl font-bold text-white uppercase">{{ config('site.name') }}</h1>
            <p class="text-gray-400 text-sm mt-1">Panel Administrasi</p>
        </div>

        <div class="card bg-base-100 shadow-2xl">
            <div class="card-body p-8">
                <h2 class="text-xl font-semibold text-base-content text-center mb-1">Masuk ke Akun Anda</h2>
                <p class="text-base-content/50 text-sm text-center mb-6">Silakan masukkan kredensial Anda</p>

                @if ($errors->any())
                    <div class="alert alert-error mb-4">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm">{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-control mb-4">
                        <label class="label" for="username">
                            <span class="label-text font-medium text-base-content">Username</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2 @error('username') input-error @enderror">
                            <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <input type="text" name="username" id="username" value="{{ old('username') }}" class="grow" placeholder="Masukkan username" required autofocus />
                        </label>
                    </div>
                    <div class="form-control mb-4">
                        <label class="label" for="password">
                            <span class="label-text font-medium text-base-content">Password</span>
                        </label>
                        <label class="input input-bordered flex items-center gap-2">
                            <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <input type="password" name="password" id="password" class="grow" placeholder="Masukkan password" required />
                        </label>
                    </div>
                    <div class="form-control mb-6">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="remember" class="checkbox checkbox-sm checkbox-primary" />
                            <span class="label-text text-base-content/70">Ingat saya</span>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Masuk
                    </button>

                </form>
            </div>
        </div>
        <p class="text-center text-gray-500 text-xs mt-6">&copy; {{ date('Y') }} {{ config('site.name') }}</p>
    </div>
</body>
</html>
