<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title')</title>

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#111827">
    <meta name="description" content="Sistem Point of Sales untuk Minimarket — Kasir, Stok, Laporan, Barcode">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192x192.svg') }}">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    @yield('extra_css')
</head>

@php
$authUser = auth()->user();
$isAdmin = $authUser && $authUser->role === 'admin';
$isSupervisor = $authUser && $authUser->role === 'supervisor';
$isCashier = $authUser && $authUser->role === 'cashier';
@endphp

<body class="bg-gray-100">

    {{-- PWA Install Banner --}}
    <div id="pwa-install-banner"
        style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:9998;
               background:linear-gradient(135deg,#1e3a5f,#2563eb);color:white;
               padding:12px 16px;display:none;align-items:center;justify-content:space-between;gap:12px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <svg style="width:20px;height:20px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.5l-6-6m6 6l6-6m-6 6V5.5" />
            </svg>
            <span style="font-size:0.9rem;">Pasang MiniPOS di perangkat ini untuk pengalaman terbaik!</span>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0;">
            <button id="pwa-install-btn"
                style="background:white;color:#1e3a5f;font-weight:700;font-size:0.8rem;padding:6px 14px;border-radius:8px;border:none;cursor:pointer;">
                Pasang
            </button>
            <button id="pwa-dismiss-btn"
                style="background:rgba(255,255,255,.15);color:white;font-size:0.8rem;padding:6px 10px;border-radius:8px;border:none;cursor:pointer;">
                Nanti
            </button>
        </div>
    </div>

    {{-- ================================================================
         TOAST HOST — fixed, top-right, stack auto-dismiss
         ================================================================ --}}
    <div id="toast-host"
        x-data="toastSystem()"
        x-init="window.toastApp = $data"
        class="fixed flex flex-col gap-2"
        style="top:1rem; right:1rem; z-index:9999; max-width:360px; width:calc(100vw - 2rem); pointer-events:none;">

        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.visible"
                x-transition
                :class="toastClass(toast.type)"
                class="toast-item rounded-xl shadow-2xl border flex items-start gap-3 px-4 py-3 cursor-pointer select-none"
                style="pointer-events:auto;"
                @click="remove(toast.id)">

                {{-- Icon --}}
                <div class="flex-shrink-0 mt-0.5" x-html="toastIcon(toast.type)"></div>

                {{-- Body --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold leading-tight" x-text="toastTitle(toast.type)"></p>
                    <p class="text-xs mt-0.5 leading-snug" style="opacity:.9" x-text="toast.message"></p>
                </div>

                {{-- Close --}}
                <button class="flex-shrink-0 mt-0.5 transition-opacity" style="opacity:.6;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.6">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Progress bar --}}
                <div class="toast-progress" :style="`animation-duration:${toast.duration}ms`"></div>
            </div>
        </template>
    </div>

    {{-- ================================================================
         APP LAYOUT — sidebar + main, managed by Alpine appLayout()
         ================================================================ --}}
    <div class="flex h-screen bg-gray-100 overflow-hidden"
        x-data="appLayout()"
        x-init="initLayout()">

        @if($isCashier){{-- ========== CASHIER: No sidebar, full width ========== --}}@endif

        @if(!$isCashier)
        {{-- Mobile backdrop overlay --}}
        <div x-show="sidebarOpen && isMobile"
            x-cloak
            x-transition
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black bg-opacity-50"
            style="z-index:998;"></div>

        {{-- ============================================================
             SIDEBAR
             ============================================================ --}}
        <aside id="app-sidebar"
            :style="sidebarStyle"
            class="bg-gray-900 text-white shadow-lg overflow-y-auto overflow-x-hidden flex-shrink-0">

            {{-- Logo + collapse toggle --}}
            <div class="flex items-center border-b border-gray-800 h-16 px-3 gap-2">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div x-show="!sidebarCollapsed" class="min-w-0">
                        <p class="font-bold text-sm text-white leading-tight whitespace-nowrap">POS System</p>
                        <p class="text-gray-400 text-xs whitespace-nowrap truncate">{{ env('STORE_NAME', 'Minimarket') }}</p>
                    </div>
                </div>
                {{-- Desktop collapse button (hidden on mobile via Alpine) --}}
                <button x-show="!isMobile"
                    x-cloak
                    @click="toggleDesktop()"
                    title="Perkecil / Perbesar sidebar"
                    class="flex items-center justify-center w-7 h-7 rounded-md hover:bg-gray-800 transition flex-shrink-0">
                    <svg width="16" height="16" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"
                        :style="sidebarCollapsed ? 'transform:rotate(180deg);transition:transform .3s' : 'transform:rotate(0deg);transition:transform .3s'">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="py-4 px-2 space-y-0.5">

                {{-- Dashboard (Admin + Supervisor) --}}
                @if(!$isCashier)
                <a href="{{ route('dashboard') }}"
                    :class="sidebarCollapsed ? 'justify-center px-2' : 'px-3'"
                    class="nav-link group relative flex items-center py-2.5 rounded-lg @if(request()->routeIs('dashboard')) bg-blue-600 text-white @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-3m0 0l7-4 7 4m-7-4v12m0 0l-7-4m7 4l7-4" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="ml-3 whitespace-nowrap">Dashboard</span>
                    <span x-show="sidebarCollapsed" x-cloak class="sidebar-tooltip">Dashboard</span>
                </a>
                @endif

                {{-- Kasir POS (all roles) --}}
                <a href="{{ route('pos.index') }}"
                    :class="sidebarCollapsed ? 'justify-center px-2' : 'px-3'"
                    class="nav-link group relative flex items-center py-2.5 rounded-lg @if(request()->routeIs('pos.*')) bg-blue-600 text-white @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="ml-3 whitespace-nowrap">Kasir POS</span>
                    <span x-show="sidebarCollapsed" x-cloak class="sidebar-tooltip">Kasir POS</span>
                </a>

                {{-- Master Barang (Admin only) --}}
                @if($isAdmin)
                <a href="{{ route('master.index') }}"
                    :class="sidebarCollapsed ? 'justify-center px-2' : 'px-3'"
                    class="nav-link group relative flex items-center py-2.5 rounded-lg @if(request()->routeIs('master.*')) bg-blue-600 text-white @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="ml-3 whitespace-nowrap">Master Barang</span>
                    <span x-show="sidebarCollapsed" x-cloak class="sidebar-tooltip">Master Barang</span>
                </a>
                <div x-show="!sidebarCollapsed" class="ml-4 space-y-1">
                    <a href="{{ route('categories.index') }}"
                        class="flex items-center p-2 pl-3 rounded-lg hover:bg-gray-800 text-sm text-gray-300 @if(request()->routeIs('categories.*')) bg-gray-700 text-white @endif">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        Kategori Barang
                    </a>
                </div>
                @endif

                {{-- Stok Barang (Admin + Supervisor) --}}
                @if(!$isCashier)
                <a href="{{ route('stock.index') }}"
                    :class="sidebarCollapsed ? 'justify-center px-2' : 'px-3'"
                    class="nav-link group relative flex items-center py-2.5 rounded-lg @if(request()->routeIs('stock.*')) bg-blue-600 text-white @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="ml-3 whitespace-nowrap">Stok Barang</span>
                    <span x-show="sidebarCollapsed" x-cloak class="sidebar-tooltip">Stok Barang</span>
                </a>

                {{-- Penawaran (Admin + Supervisor) --}}
                <a href="{{ route('quotation.index') }}"
                    :class="sidebarCollapsed ? 'justify-center px-2' : 'px-3'"
                    class="nav-link group relative flex items-center py-2.5 rounded-lg @if(request()->routeIs('quotation.*')) bg-blue-600 text-white @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="ml-3 whitespace-nowrap">Penawaran Barang</span>
                    <span x-show="sidebarCollapsed" x-cloak class="sidebar-tooltip">Penawaran</span>
                </a>

                {{-- Laporan (Admin + Supervisor) --}}
                <a href="{{ route('report.index') }}"
                    :class="sidebarCollapsed ? 'justify-center px-2' : 'px-3'"
                    class="nav-link group relative flex items-center py-2.5 rounded-lg @if(request()->routeIs('report.*')) bg-blue-600 text-white @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="ml-3 whitespace-nowrap">Laporan Penjualan</span>
                    <span x-show="sidebarCollapsed" x-cloak class="sidebar-tooltip">Laporan</span>
                </a>

                {{-- Barcode (Admin + Supervisor) --}}
                <a href="{{ route('barcode.index') }}"
                    :class="sidebarCollapsed ? 'justify-center px-2' : 'px-3'"
                    class="nav-link group relative flex items-center py-2.5 rounded-lg @if(request()->routeIs('barcode.*')) bg-blue-600 text-white @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span x-show="!sidebarCollapsed" class="ml-3 whitespace-nowrap">Cetak Barcode</span>
                    <span x-show="sidebarCollapsed" x-cloak class="sidebar-tooltip">Barcode</span>
                </a>
                @endif

                {{-- Pengaturan (Admin only) --}}
                @if($isAdmin)
                <div class="border-t border-gray-800 pt-2 mt-4">
                    <a x-show="sidebarCollapsed" x-cloak href="{{ route('setting.profile') }}"
                        class="nav-link group relative flex items-center justify-center px-2 py-2.5 rounded-lg @if(request()->routeIs('setting.*')||request()->routeIs('users.*')) bg-blue-600 text-white @endif">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="sidebar-tooltip">Pengaturan</span>
                    </a>
                    <div x-show="!sidebarCollapsed"
                        x-data="{ open: {{ (request()->routeIs('setting.*') || request()->routeIs('users.*')) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full nav-link flex items-center px-3 py-2.5 rounded-lg @if(request()->routeIs('setting.*')||request()->routeIs('users.*')) bg-blue-600 text-white @endif">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="ml-3 flex-1 text-left whitespace-nowrap">Pengaturan</span>
                            <svg class="w-4 h-4 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" class="ml-4 mt-1 space-y-1">
                            <a href="{{ route('setting.profile') }}"
                                class="flex items-center p-2 pl-3 rounded-lg hover:bg-gray-800 text-sm text-gray-300 @if(request()->routeIs('setting.profile')) bg-gray-700 text-white @endif">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Profil Toko
                            </a>
                            <a href="{{ route('setting.units') }}"
                                class="flex items-center p-2 pl-3 rounded-lg hover:bg-gray-800 text-sm text-gray-300 @if(request()->routeIs('setting.units*')) bg-gray-700 text-white @endif">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Satuan Barang
                            </a>
                            <a href="{{ route('users.index') }}"
                                class="flex items-center p-2 pl-3 rounded-lg hover:bg-gray-800 text-sm text-gray-300 @if(request()->routeIs('users.*')) bg-gray-700 text-white @endif">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Manajemen User
                            </a>
                            <a href="{{ route('setting.backup') }}"
                                class="flex items-center p-2 pl-3 rounded-lg hover:bg-gray-800 text-sm text-gray-300 @if(request()->routeIs('setting.backup*')) bg-gray-700 text-white @endif">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Backup Database
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                {{-- User info + Logout (bottom of sidebar) --}}
                <div class="border-t border-gray-800 pt-3 mt-4">
                    <div x-show="!sidebarCollapsed" class="px-3 mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-xs text-white
                                {{ $isAdmin ? 'bg-red-500' : ($isSupervisor ? 'bg-yellow-500' : 'bg-green-500') }}">
                                {{ strtoupper(substr($authUser->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-white truncate">{{ $authUser->name }}</p>
                                <p class="text-xs text-gray-400 capitalize">{{ $authUser->role }}</p>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            :class="sidebarCollapsed ? 'justify-center px-2' : 'px-3'"
                            class="w-full nav-link flex items-center py-2.5 rounded-lg text-red-400 hover:bg-red-900/30 hover:text-red-300 transition">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="ml-3 whitespace-nowrap">Keluar</span>
                            <span x-show="sidebarCollapsed" x-cloak class="sidebar-tooltip">Keluar</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>
        @endif {{-- end !$isCashier --}}
        <div id="app-main" class="flex-1 flex flex-col overflow-hidden min-w-0">

            {{-- Top Header --}}
            <header class="bg-white shadow-sm border-b px-4 h-16 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    {{-- Mobile hamburger (hidden for cashier since no sidebar) --}}
                    @if(!$isCashier)
                    <button x-show="isMobile"
                        x-cloak
                        @click="sidebarOpen = true"
                        class="p-2 -ml-1 rounded-lg hover:bg-gray-100 transition"
                        title="Buka menu">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    @endif
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 leading-tight">@yield('page_title')</h2>
                        <p class="text-gray-500 text-xs">@yield('page_subtitle')</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @yield('header_actions')
                    {{-- User info --}}
                    <div class="text-right hidden sm:flex flex-col items-end">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-800 text-sm">{{ $authUser->name }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                {{ $isAdmin ? 'bg-red-100 text-red-700' : ($isSupervisor ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                {{ ucfirst($authUser->role) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500" id="current-time"></p>
                    </div>
                    {{-- Avatar --}}
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs text-white flex-shrink-0
                        {{ $isAdmin ? 'bg-red-500' : ($isSupervisor ? 'bg-yellow-500' : 'bg-green-500') }}">
                        {{ strtoupper(substr($authUser->name, 0, 1)) }}
                    </div>
                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 hover:bg-red-50 hover:text-red-600 text-gray-500 rounded-full transition" title="Keluar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Content Area --}}
            <main id="content-area" class="flex-1 overflow-y-auto p-4 md:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        /* ── Time display ─────────────────────────────────────── */
        function updateTime() {
            const el = document.getElementById('current-time');
            if (el) el.textContent = new Date().toLocaleDateString('id-ID', {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        updateTime();
        setInterval(updateTime, 60000);

        /* ── App Layout (sidebar state) ───────────────────────── */
        function appLayout() {
            return {
                sidebarCollapsed: false,
                sidebarOpen: false,
                isMobile: false,

                initLayout() {
                    this.isMobile = window.innerWidth < 768;
                    const saved = localStorage.getItem('sidebar-collapsed');
                    if (saved !== null) this.sidebarCollapsed = (saved === 'true');
                    window.addEventListener('resize', () => {
                        const wasM = this.isMobile;
                        this.isMobile = window.innerWidth < 768;
                        if (!this.isMobile && wasM) this.sidebarOpen = false;
                    });
                },

                toggleDesktop() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('sidebar-collapsed', String(this.sidebarCollapsed));
                },

                get sidebarStyle() {
                    const tr = 'width .3s ease, transform .3s ease';
                    if (this.isMobile) {
                        const tx = this.sidebarOpen ? '0' : '-100%';
                        return `width:16rem; transform:translateX(${tx}); transition:${tr};`;
                    }
                    const w = this.sidebarCollapsed ? '4rem' : '16rem';
                    return `width:${w}; transform:translateX(0); transition:${tr};`;
                }
            };
        }

        /* ── Toast System ─────────────────────────────────────── */
        function toastSystem() {
            return {
                toasts: [],
                _id: 1,

                add(message, type, duration) {
                    type = type || 'info';
                    duration = duration || (type === 'error' ? 6000 : type === 'warning' ? 5000 : 4000);
                    const id = this._id++;
                    this.toasts.push({
                        id,
                        message,
                        type,
                        visible: true,
                        duration
                    });
                    setTimeout(() => this.remove(id), duration);
                },

                remove(id) {
                    const t = this.toasts.find(t => t.id === id);
                    if (t) {
                        t.visible = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }, 350);
                    }
                },

                toastClass(type) {
                    return {
                        success: 'toast-success',
                        error: 'toast-error',
                        warning: 'toast-warning',
                        info: 'toast-info',
                    } [type] || 'toast-info';
                },

                toastTitle(type) {
                    return {
                        success: 'Berhasil',
                        error: 'Gagal',
                        warning: 'Perhatian',
                        info: 'Informasi',
                    } [type] || 'Info';
                },

                toastIcon(type) {
                    const ic = {
                        success: '<svg width="18" height="18" fill="#4ade80" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                        error: '<svg width="18" height="18" fill="#f87171" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                        warning: '<svg width="18" height="18" fill="#fbbf24" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                        info: '<svg width="18" height="18" fill="#60a5fa" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
                    };
                    return ic[type] || ic.info;
                }
            };
        }

        /* ── Global helper ────────────────────────────────────── */
        window.showToast = function(msg, type, dur) {
            if (window.toastApp) {
                window.toastApp.add(msg, type, dur);
            } else {
                document.addEventListener('alpine:initialized', function() {
                    window.toastApp && window.toastApp.add(msg, type, dur);
                }, {
                    once: true
                });
            }
        };

        /* ── Flash session → toast ────────────────────────────── */
        @if(session('success'))
            <script>
                document.addEventListener('alpine:initialized', function() {
                    window.toastApp && window.toastApp.add(@json(session('success')), 'success');
                }, {
                    once: true
                });
            </script>
        @endif
        @if(session('error'))
            <script>
                document.addEventListener('alpine:initialized', function() {
                    window.toastApp && window.toastApp.add(@json(session('error')), 'error');
                }, {
                    once: true
                });
            </script>
        @endif
        @if(session('warning'))
            <script>
                document.addEventListener('alpine:initialized', function() {
                    window.toastApp && window.toastApp.add(@json(session('warning')), 'warning');
                }, {
                    once: true
                });
            </script>
        @endif
        @if(session('info'))
            <script>
                document.addEventListener('alpine:initialized', function() {
                    window.toastApp && window.toastApp.add(@json(session('info')), 'info');
                }, {
                    once: true
                });
            </script>
        @endif
    </script>

    {{-- PWA Service Worker Registration + Install Prompt --}}
    <script>
        /* ── Service Worker ──────────────────────────────────── */
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js', {
                        scope: '/'
                    })
                    .then(function() {
                        console.log('[PWA] SW registered');
                    })
                    .catch(function(e) {
                        console.warn('[PWA] SW failed:', e);
                    });
            });
        }

        /* ── PWA Install Banner ───────────────────────────────── */
        (function() {
            var deferredPrompt = null;
            var banner = document.getElementById('pwa-install-banner');
            var dismissed = sessionStorage.getItem('pwa-banner-dismissed');

            if (!dismissed) {
                window.addEventListener('beforeinstallprompt', function(e) {
                    e.preventDefault();
                    deferredPrompt = e;
                    if (banner) banner.style.display = 'flex';
                });
            }

            document.getElementById('pwa-install-btn') && document.getElementById('pwa-install-btn').addEventListener('click', function() {
                if (!deferredPrompt) return;
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function(c) {
                    if (banner) banner.style.display = 'none';
                    deferredPrompt = null;
                });
            });

            document.getElementById('pwa-dismiss-btn') && document.getElementById('pwa-dismiss-btn').addEventListener('click', function() {
                if (banner) banner.style.display = 'none';
                sessionStorage.setItem('pwa-banner-dismissed', '1');
            });

            window.addEventListener('appinstalled', function() {
                if (banner) banner.style.display = 'none';
                console.log('[PWA] App installed');
            });
        })();
    </script>

    @yield('extra_js')
    <script>
        // Prevent Esc from exiting fullscreen except via explicit button
        document.addEventListener('keydown', function(e) {
            if ((e.key === 'Escape' || e.keyCode === 27) && document.fullscreenElement) {
                // Only close modal/dropdown, do not exit fullscreen
                e.preventDefault();
                // Optionally, trigger custom close modal logic here if needed
            }
        });
        // Global showToast fallback for non-Alpine pages
        if (!window.showToast) {
            window.showToast = function(msg, type, dur) {
                alert(msg);
            };
        }
    </script>
</body>

</html>