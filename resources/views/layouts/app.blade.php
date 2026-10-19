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
                    <div class="sb-nav-section">ADMIN / MENTOR</div>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('mahasiswas.index') }}" class="{{ request()->routeIs('mahasiswas.*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Pendaftar / Mahasiswa
                    </a>
                    <a href="{{ route('penempatan.index') }}" class="{{ request()->routeIs('penempatan.*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/><line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/><line x1="9" y1="14" x2="9" y2="14.01"/><line x1="15" y1="14" x2="15" y2="14.01"/><line x1="9" y1="18" x2="15" y2="18"/></svg>
                        Penempatan
                    </a>
                    <a href="{{ route('presensis.index') }}" class="{{ request()->routeIs('presensis.*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M9 16l2 2 4-4"/></svg>
                        Rekap Presensi
                    </a>
                    <a href="{{ route('logbooks.index') }}" class="{{ request()->routeIs('logbooks.*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        Logbook &amp; Monitoring
                    </a>
                    <a href="{{ route('penilaian.index') }}" class="{{ request()->routeIs('penilaian.*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        Penilaian
                    </a>
                    <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        Laporan Akhir
                    </a>
                    <a href="{{ route('sertifikasi.index') }}" class="{{ request()->routeIs('sertifikasi.*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                        Sertifikasi
                    </a>
                    <a href="{{ route('pengguna.index') }}" class="{{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Akun Admin / Mentor
                    </a>
                @else
                    @php
                        $mhsSidebar = auth()->user()->mahasiswa;
                        $statusPendaftaran = $mhsSidebar->status_pendaftaran ?? 'belum_daftar';
                        $sudahDiterima = $mhsSidebar && in_array($statusPendaftaran, ['diterima', 'ditempatkan']);
                        $sudahDitempatkan = $mhsSidebar && ($statusPendaftaran === 'ditempatkan' || $mhsSidebar->status === 'selesai');
                    @endphp
                    <div class="sb-nav-section">MAHASISWA</div>
                    <a href="{{ route('mahasiswa.dashboard') }}" class="{{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 22"/></svg>
                        Dashboard
                    </a>

                    {{-- Surat Penerimaan Magang --}}
                    @if($sudahDiterima)
                        <a href="{{ route('penempatan.index') }}" class="{{ request()->routeIs('penempatan.*') ? 'active' : '' }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            Surat Penerimaan Magang
                        </a>
                    @else
                        <div title="Tersedia setelah Anda dinyatakan diterima oleh Cabang" style="display:flex; align-items:center; gap:12px; padding:11px 14px; border-radius:8px; font-size:13.5px; color:rgba(255,255,255,0.3); cursor:not-allowed; user-select:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.4;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            Surat Penerimaan Magang
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:auto; opacity:.4;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                    @endif

                    {{-- Menu Kegiatan Magang (Terbuka setelah Ditempatkan oleh Admin) --}}
                    @if($sudahDitempatkan)
                        <a href="{{ route('presensis.index') }}" class="{{ request()->routeIs('presensis.*') ? 'active' : '' }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M9 16l2 2 4-4"/></svg>
                            Presensi
                        </a>
                        <a href="{{ route('logbooks.index') }}" class="{{ request()->routeIs('logbooks.*') ? 'active' : '' }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                            Logbook Harian
                        </a>
                        <a href="{{ route('penilaian.index') }}" class="{{ request()->routeIs('penilaian.*') ? 'active' : '' }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            Penilaian
                        </a>
                        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            Laporan Akhir
                        </a>
                        <a href="{{ route('sertifikasi.index') }}" class="{{ request()->routeIs('sertifikasi.*') ? 'active' : '' }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                            Sertifikasi
                        </a>
                    @else
                        <div title="Tersedia setelah Anda ditempatkan di cabang/unit oleh Admin" style="display:flex; align-items:center; gap:12px; padding:11px 14px; border-radius:8px; font-size:13.5px; color:rgba(255,255,255,0.3); cursor:not-allowed; user-select:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.4;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M9 16l2 2 4-4"/></svg>
                            Presensi
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:auto; opacity:.4;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                        <div title="Tersedia setelah Anda ditempatkan di cabang/unit oleh Admin" style="display:flex; align-items:center; gap:12px; padding:11px 14px; border-radius:8px; font-size:13.5px; color:rgba(255,255,255,0.3); cursor:not-allowed; user-select:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.4;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                            Logbook Harian
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:auto; opacity:.4;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                        <div title="Tersedia setelah Anda ditempatkan di cabang/unit oleh Admin" style="display:flex; align-items:center; gap:12px; padding:11px 14px; border-radius:8px; font-size:13.5px; color:rgba(255,255,255,0.3); cursor:not-allowed; user-select:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.4;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            Penilaian
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:auto; opacity:.4;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                        <div title="Tersedia setelah Anda ditempatkan di cabang/unit oleh Admin" style="display:flex; align-items:center; gap:12px; padding:11px 14px; border-radius:8px; font-size:13.5px; color:rgba(255,255,255,0.3); cursor:not-allowed; user-select:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.4;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            Laporan Akhir
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:auto; opacity:.4;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                        <div title="Tersedia setelah Anda ditempatkan di cabang/unit oleh Admin" style="display:flex; align-items:center; gap:12px; padding:11px 14px; border-radius:8px; font-size:13.5px; color:rgba(255,255,255,0.3); cursor:not-allowed; user-select:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.4;"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                            Sertifikasi
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:auto; opacity:.4;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                    @endif
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
