<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMMM - Sistem Informasi Manajemen Mahasiswa Magang')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/simmm.css') }}">
    @stack('styles')
</head>
<body>
    @php
        $user = auth()->user();
        $userRole = $user->role ?? 'mahasiswa';
        $userName = $user->name ?? 'Pengguna';
        $userInitials = collect(explode(' ', trim($userName)))->filter()->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('');
        $dashboardRoute = $userRole === 'admin' ? route('admin.dashboard') : route('mahasiswa.dashboard');
    @endphp

    <!-- Mobile Header Navigation -->
    <div class="mobile-header">
        <div style="display:flex; align-items:center; gap:10px;">
            <img src="{{ asset('images/logo_bsi.png') }}" alt="Logo BSI" style="height:28px;">
            <span style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:15px; color:#fff;">SIMMM BSI</span>
        </div>
        <button type="button" class="mobile-toggle" id="sidebar-toggle" aria-label="Buka Menu">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
    </div>
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <div class="app-shell">
        <aside class="sidebar" id="app-sidebar">
            <div class="sb-brand">
                <div class="mark"><img src="{{ asset('images/logo_bsi.png') }}" alt="Logo BSI"></div>
                <div>
                    <div class="t1">SIMMM BSI</div>
                    <div class="t2">AREA YOGYAKARTA</div>
                </div>
            </div>

            <nav class="sb-nav">
                @if($userRole === 'admin')
                    @include('components.sidebar-admin')
                @else
                    @include('components.sidebar-mahasiswa')
                @endif
            </nav>

            <div class="sb-foot">
                <a href="{{ route('profil.show') }}" class="sb-user-menu-btn {{ request()->routeIs('profil.*') ? 'active' : '' }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Lihat Profil Saya</span>
                </a>
                
                <form method="POST" action="{{ route('logout') }}" data-confirm="Apakah Anda yakin ingin keluar dari akun SIMMM BSI?" data-title="Konfirmasi Keluar" data-button="Ya, Keluar Akun">
                    @csrf
                    <button type="submit" class="sb-user-menu-btn sb-user-menu-btn-danger">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        <span style="font-weight:600;">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="main page-transition-root">
            @yield('content')
        </main>
    </div>

    <!-- Custom Confirmation Modal System -->
    <div id="custom-confirm-modal" class="modal-overlay" aria-hidden="true">
        <div class="modal-box" role="dialog" aria-modal="true">
            <div class="modal-icon" id="modal-confirm-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="modal-title" id="modal-confirm-title">Konfirmasi</div>
            <div class="modal-message" id="modal-confirm-message">Apakah Anda yakin ingin melanjutkan tindakan ini?</div>
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" id="modal-cancel-btn">Batal</button>
                <button type="button" class="btn btn-danger" id="modal-ok-btn">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/simmm.js') }}"></script>
    @stack('scripts')
</body>
</html>
