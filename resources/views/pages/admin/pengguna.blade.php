@extends('layouts.app')

@section('title', 'SIMMM - Manajemen Akun Admin & Mentor')

@section('content')
<x-topbar title="Manajemen Akun Admin/Mentor" subtitle="Kelola daftar akun pengelola, admin cabang, dan mentor pembimbing magang BSI.">
    <x-slot:actions>
        <button type="button" class="btn btn-solid" onclick="openTambahAdminModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Tambah Akun Admin/Mentor</span>
        </button>
    </x-slot:actions>
</x-topbar>

<div id="flash-container">
    @if (session('success'))
        <div class="flash">
            <div style="display:flex; align-items:center; gap:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div class="flash" style="background:var(--danger-bg); color:var(--danger); border-color:#efc0b9;">
            <div style="display:flex; align-items:center; gap:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="flash" style="background:var(--danger-bg); color:var(--danger); border-color:#efc0b9;">
            <div style="display:flex; align-items:center; gap:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    <strong>Terdapat kesalahan:</strong>
                    <ul style="margin:4px 0 0 16px; padding:0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
        </div>
    @endif
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Daftar Akun Pengelola &amp; Mentor BSI</h3>
            <div class="card-sub">Hak akses pengelola sistem SIMMM BSI Area Yogyakarta.</div>
        </div>
    </div>

    @if($users->isEmpty())
        <div class="empty-state">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <h3>Belum Ada Akun Admin/Mentor</h3>
            <p>Klik tombol "Tambah Akun Admin/Mentor" untuk mendaftarkan akun pengelola baru.</p>
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nama &amp; Identitas</th>
                        <th>Alamat Email</th>
                        <th>Peran / Hak Akses</th>
                        <th>Tanggal Terdaftar</th>
                        <th>Status Akun</th>
                        <th>Aksi Kelola</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $usr)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px;">
                                        {{ collect(explode(' ', trim($usr->name)))->filter()->map(fn($p) => strtoupper(substr($p, 0, 1)))->take(2)->implode('') ?: 'AD' }}
                                    </div>
                                    <div>
                                        <strong>{{ $usr->name }}</strong>
                                        @if($usr->id === auth()->id())
                                            <span style="font-size:11px; color:var(--primary); font-weight:600; background:var(--primary-light); padding:2px 6px; border-radius:4px; margin-left:4px;">Saya (Aktif)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $usr->email }}</td>
                            <td>
                                <span class="badge badge-ok" style="background:var(--primary-light); color:var(--primary-dark);">
                                    {{ strtoupper($usr->role ?? 'ADMIN') }}
                                </span>
                            </td>
                            <td>{{ $usr->created_at ? $usr->created_at->format('d-m-Y H:i') : '-' }}</td>
                            <td>
                                @if(($usr->status ?? 'aktif') === 'aktif')
                                    <span class="badge badge-ok">AKTIF</span>
                                @else
                                    <span class="badge badge-danger">NONAKTIF</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    {{-- Tombol Reset Password --}}
                                    <button type="button" class="btn btn-ghost btn-sm" onclick="openResetPasswordModal({{ $usr->id }}, '{{ e($usr->name) }}')">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                        <span>Reset Password</span>
                                    </button>

                                    {{-- Tombol Toggle Status (Nonaktifkan/Aktifkan) --}}
                                    @if($usr->id !== auth()->id())
                                        <form method="POST" action="{{ route('pengguna.toggle-status', $usr->id) }}" 
                                              data-confirm="Apakah Anda yakin ingin {{ ($usr->status ?? 'aktif') === 'aktif' ? 'MENONAKTIFKAN' : 'MENGAKTIFKAN' }} akun {{ $usr->name }}?" 
                                              data-title="Konfirmasi Perubahan Status Akun" 
                                              data-button="Ya, {{ ($usr->status ?? 'aktif') === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}"
                                              data-variant="{{ ($usr->status ?? 'aktif') === 'aktif' ? 'danger' : 'primary' }}">
                                            @csrf
                                            @method('PUT')
                                            @if(($usr->status ?? 'aktif') === 'aktif')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Nonaktifkan Akun
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-solid btn-sm">
                                                    Aktifkan Akun
                                                </button>
                                            @endif
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="pagination-container">
                {{ $users->links() }}
            </div>
        @endif
    @endif
</div>

{{-- Modal Tambah Akun Admin / Mentor --}}
<div id="tambah-admin-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:500px;" role="dialog" aria-modal="true" aria-labelledby="modal-tambah-title">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--line); padding-bottom:14px;">
            <div>
                <div id="modal-tambah-title" class="modal-title" style="margin:0; font-size:18px; font-weight:700; color:var(--primary-dark);">Tambah Akun Admin/Mentor Baru</div>
                <div style="font-size:12px; color:var(--muted); margin-top:2px;">Buat kredensial akses untuk pengelola atau mentor BSI.</div>
            </div>
            <button type="button" onclick="closeTambahAdminModal()" aria-label="Tutup modal" style="background:none; border:none; cursor:pointer; color:var(--muted); font-size:24px; line-height:1; padding:0 4px;">&times;</button>
        </div>

        <form action="{{ route('pengguna.store') }}" method="POST">
            @csrf

            <div style="margin-bottom:14px;">
                <label class="form-label">Nama Lengkap Admin/Mentor <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Ahmad Fauzi, S.E." required>
            </div>

            <div style="margin-bottom:14px;">
                <label class="form-label">Alamat Email Login <span style="color:var(--danger);">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Contoh: ahmad.fauzi@bankbsi.co.id" required>
            </div>

            <div style="margin-bottom:20px;">
                <label class="form-label">Kata Sandi Default <span style="color:var(--danger);">*</span></label>
                <input type="password" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                <div style="font-size:11.5px; color:var(--muted); margin-top:4px;">Kata sandi ini dapat diubah pengguna setelah login ke sistem.</div>
            </div>

            <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--line); padding-top:16px;">
                <button type="button" class="btn btn-ghost" onclick="closeTambahAdminModal()">Batal</button>
                <button type="submit" class="btn btn-solid">Simpan Akun Baru</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Reset Password Admin/Mentor --}}
<div id="reset-password-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:480px;" role="dialog" aria-modal="true" aria-labelledby="modal-reset-title">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--line); padding-bottom:14px;">
            <div>
                <div id="modal-reset-title" class="modal-title" style="margin:0; font-size:18px; font-weight:700; color:var(--primary-dark);">Reset Password Akun</div>
                <div style="font-size:12px; color:var(--muted); margin-top:2px;" id="reset-modal-sub">Atur ulang kata sandi pengguna.</div>
            </div>
            <button type="button" onclick="closeResetPasswordModal()" aria-label="Tutup modal" style="background:none; border:none; cursor:pointer; color:var(--muted); font-size:24px; line-height:1; padding:0 4px;">&times;</button>
        </div>

        <form id="reset-password-form" method="POST" action="">
            @csrf
            @method('PUT')

            <div style="margin-bottom:14px;">
                <label class="form-label">Kata Sandi Baru <span style="color:var(--danger);">*</span></label>
                <input type="password" name="password" placeholder="Masukkan kata sandi baru (min 6 karakter)" required minlength="6">
            </div>

            <div style="margin-bottom:20px;">
                <label class="form-label">Konfirmasi Kata Sandi Baru <span style="color:var(--danger);">*</span></label>
                <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi baru" required minlength="6">
            </div>

            <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--line); padding-top:16px;">
                <button type="button" class="btn btn-ghost" onclick="closeResetPasswordModal()">Batal</button>
                <button type="submit" class="btn btn-solid">Simpan Password Baru</button>
            </div>
        </form>
    </div>
</div>

<script>
    const tambahAdminModal = document.getElementById('tambah-admin-modal');
    const resetPasswordModal = document.getElementById('reset-password-modal');
    const resetPasswordForm = document.getElementById('reset-password-form');
    const resetModalSub = document.getElementById('reset-modal-sub');

    function openTambahAdminModal() {
        if (!tambahAdminModal) return;
        tambahAdminModal.classList.add('active');
        tambahAdminModal.setAttribute('aria-hidden', 'false');
    }

    function closeTambahAdminModal() {
        if (!tambahAdminModal) return;
        tambahAdminModal.classList.remove('active');
        tambahAdminModal.setAttribute('aria-hidden', 'true');
    }

    function openResetPasswordModal(userId, userName) {
        if (!resetPasswordModal || !resetPasswordForm) return;
        resetPasswordForm.action = `/pengguna/${userId}/reset-password`;
        if (resetModalSub) resetModalSub.textContent = `Atur ulang kata sandi untuk akun ${userName}.`;
        resetPasswordModal.classList.add('active');
        resetPasswordModal.setAttribute('aria-hidden', 'false');
    }

    function closeResetPasswordModal() {
        if (!resetPasswordModal) return;
        resetPasswordModal.classList.remove('active');
        resetPasswordModal.setAttribute('aria-hidden', 'true');
    }

    document.addEventListener('DOMContentLoaded', () => {
        [tambahAdminModal, resetPasswordModal].forEach(modal => {
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        closeTambahAdminModal();
                        closeResetPasswordModal();
                    }
                });
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' || e.key === 'Esc') {
                closeTambahAdminModal();
                closeResetPasswordModal();
            }
        });
    });
</script>
@endsection
