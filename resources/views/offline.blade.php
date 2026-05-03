<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidak Ada Koneksi — {{ config('app.name') }}</title>
    <meta name="theme-color" content="#111827">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            background: #111827;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: system-ui, sans-serif;
        }

        .card {
            text-align: center;
            max-width: 420px;
            padding: 2.5rem;
        }

        .icon-wrap {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: rgba(239, 68, 68, .15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(239, 68, 68, .35);
        }

        h1 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: .5rem;
        }

        p {
            color: #9ca3af;
            font-size: .95rem;
            line-height: 1.6;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            margin-top: 1.75rem;
            padding: .7rem 1.4rem;
            background: #1d4ed8;
            color: white;
            border-radius: .75rem;
            font-weight: 600;
            font-size: .9rem;
            border: none;
            cursor: pointer;
            transition: background .2s;
            text-decoration: none;
        }

        .btn:hover {
            background: #1e40af;
        }

        .pulse {
            animation: pulse 2.5s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .45;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon-wrap pulse">
            <svg width="36" height="36" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636a9 9 0 010 12.728M15.536 8.464a5 5 0 010 7.072M4.929 4.929a9 9 0 000 14.142M8.464 8.464a5 5 0 000 7.072" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 12h.01" />
            </svg>
        </div>
        <h1>Tidak Ada Koneksi</h1>
        <p>Anda sedang <strong>offline</strong>. Periksa koneksi internet Anda, lalu coba lagi.</p>
        <a href="/" class="btn" onclick="window.location.reload();return false;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Coba Lagi
        </a>
    </div>
</body>

</html>