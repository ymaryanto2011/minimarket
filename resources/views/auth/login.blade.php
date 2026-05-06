<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Minimarket POS') }} — Login</title>
    <meta name="theme-color" content="#111827">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <style>
        /* ── Background ─────────────────────────────────────────── */
        .login-bg {
            min-height: 100vh;
            background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            position: relative;
            overflow: hidden;
        }

        /* ── Animated particles ──────────────────────────────────── */
        .particles {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.15);
            animation: float linear infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translateY(-10vh) scale(1);
                opacity: 0;
            }
        }

        /* ── Minimarket silhouette SVG background ────────────────── */
        .silhouette-wrap {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            pointer-events: none;
            opacity: 0.12;
        }

        /* ── Card ────────────────────────────────────────────────── */
        .login-card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        .login-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0.75rem;
            color: #f1f5f9;
            font-size: 0.95rem;
            transition: border-color .2s, background .2s;
            outline: none;
        }

        .login-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .login-input:focus {
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.12);
        }

        .login-input:-webkit-autofill,
        .login-input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #1e3a5f inset;
            -webkit-text-fill-color: #f1f5f9;
        }

        .login-btn {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 0.75rem;
            border: none;
            cursor: pointer;
            transition: opacity .2s, transform .1s;
            letter-spacing: 0.02em;
        }

        .login-btn:hover {
            opacity: 0.92;
        }

        .login-btn:active {
            transform: scale(0.98);
        }

        .login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .badge-admin {
            background: rgba(239, 68, 68, .2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, .3);
        }

        .badge-supervisor {
            background: rgba(234, 179, 8, .15);
            color: #fde68a;
            border: 1px solid rgba(234, 179, 8, .25);
        }

        .badge-cashier {
            background: rgba(34, 197, 94, .15);
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, .25);
        }
    </style>
</head>

<body>
    <div class="login-bg flex items-center justify-center px-4 py-8" x-data="loginPage()">

        {{-- Floating particles --}}
        <div class="particles" id="particles"></div>

        {{-- Minimarket silhouette --}}
        <div class="silhouette-wrap">
            <svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg" fill="white">
                <!-- Ground -->
                <rect x="0" y="280" width="1440" height="40" />
                <!-- Building 1 - Main store (center) -->
                <rect x="460" y="120" width="520" height="160" />
                <!-- Roof overhang -->
                <rect x="430" y="100" width="580" height="30" />
                <!-- Sign board -->
                <rect x="500" y="80" width="440" height="35" />
                <!-- Door (main) -->
                <rect x="670" y="210" width="50" height="70" />
                <rect x="730" y="210" width="50" height="70" />
                <!-- Windows -->
                <rect x="490" y="150" width="70" height="50" />
                <rect x="580" y="150" width="70" height="50" />
                <rect x="800" y="150" width="70" height="50" />
                <rect x="890" y="150" width="70" height="50" />
                <!-- Building 2 - Left side shop -->
                <rect x="170" y="170" width="280" height="110" />
                <rect x="150" y="155" width="320" height="25" />
                <rect x="200" y="200" width="50" height="40" />
                <rect x="270" y="200" width="50" height="40" />
                <rect x="360" y="200" width="50" height="40" />
                <!-- Building 3 - Right side shop -->
                <rect x="990" y="175" width="280" height="105" />
                <rect x="970" y="160" width="320" height="25" />
                <rect x="1010" y="205" width="50" height="40" />
                <rect x="1080" y="205" width="50" height="40" />
                <rect x="1170" y="205" width="50" height="40" />
                <!-- Cart icon left -->
                <circle cx="80" cy="265" r="12" />
                <circle cx="120" cy="265" r="12" />
                <rect x="55" y="230" width="90" height="30" rx="5" />
                <line x1="55" y1="230" x2="40" y2="210" stroke="white" stroke-width="8" />
                <!-- Lamp posts -->
                <rect x="400" y="180" width="8" height="100" />
                <rect x="380" y="178" width="48" height="8" rx="4" />
                <rect x="1050" y="185" width="8" height="95" />
                <rect x="1030" y="183" width="48" height="8" rx="4" />
                <!-- Small details: awning stripes -->
                <rect x="460" y="100" width="18" height="30" />
                <rect x="496" y="100" width="18" height="30" />
                <rect x="532" y="100" width="18" height="30" />
                <rect x="890" y="100" width="18" height="30" />
                <rect x="926" y="100" width="18" height="30" />
                <rect x="962" y="100" width="18" height="30" />
            </svg>
        </div>

        {{-- Login Card --}}
        <div class="login-card relative z-10">

            {{-- Logo --}}
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl shadow-lg mb-3">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">{{ config('app.name', 'Minimarket POS') }}</h1>
                <p class="text-blue-300 text-sm mt-1">Sistem Point of Sales</p>
            </div>

            {{-- Error --}}
            @if($errors->any())
            <div class="bg-red-500/20 border border-red-400/40 rounded-xl p-3 mb-4 text-red-200 text-sm flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login.post') }}" @submit="loading = true">
                @csrf

                <div class="space-y-4">
                    {{-- Username / Email --}}
                    <div>
                        <label class="block text-sm font-medium text-blue-200 mb-1.5">Username / Email</label>
                        <input type="text" name="login" value="{{ old('login') }}"
                            class="login-input" placeholder="Admin  atau  admin@minimarket.local"
                            autocomplete="username" required autofocus>
                        <p class="text-blue-300/60 text-xs mt-1">Masukkan nama user <em>atau</em> email</p>
                        @error('login')
                        <p class="text-red-300 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-blue-200 mb-1.5">Password</label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" name="password"
                                class="login-input pr-10" placeholder="Kosong = tekan Login langsung"
                                autocomplete="current-password">
                            <button type="button" @click="showPass = !showPass"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-blue-300 hover:text-white transition">
                                <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-blue-300/60 text-xs mt-1">Password default: kosong (langsung klik Masuk)</p>
                    </div>

                    {{-- Remember --}}
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-white/10 border-white/20 text-blue-500">
                        <span class="text-blue-200 text-sm">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="login-btn mt-6" :disabled="loading">
                    <span x-show="!loading">Masuk</span>
                    <span x-show="loading" x-cloak class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        Memproses...
                    </span>
                </button>
            </form>

            {{-- Role info --}}
            <div class="mt-6 pt-4 border-t border-white/10">
                <p class="text-center text-blue-300/60 text-xs mb-3">Level Pengguna</p>
                <div class="flex justify-center gap-2 flex-wrap">
                    <span class="role-badge badge-admin">Admin</span>
                    <span class="role-badge badge-supervisor">Supervisor</span>
                    <span class="role-badge badge-cashier">Kasir</span>
                </div>
            </div>

            <p class="text-center text-blue-400/40 text-xs mt-4">
                &copy; {{ date('Y') }} {{ config('app.name') }}
            </p>
        </div>
    </div>

    <script>
        function loginPage() {
            return {
                showPass: false,
                loading: false
            };
        }

        // Particles
        (function() {
            const wrap = document.getElementById('particles');
            for (let i = 0; i < 18; i++) {
                const el = document.createElement('div');
                el.className = 'particle';
                const size = Math.random() * 30 + 8;
                el.style.cssText = `
                width:${size}px; height:${size}px;
                left:${Math.random()*100}%;
                animation-duration:${Math.random()*15+8}s;
                animation-delay:${Math.random()*10}s;
            `;
                wrap.appendChild(el);
            }
        })();

        // PWA Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js', {
                scope: '/'
            }).catch(() => {});
        }
    </script>
</body>

</html>