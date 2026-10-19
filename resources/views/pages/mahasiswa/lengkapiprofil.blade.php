@extends('layouts.app')

@section('title', 'SIMMM - Lengkapi Profil Akademik')

@section('content')
<x-topbar title="Lengkapi Profil Akademik" subtitle="Tahap 1 dari 3 : Pengisian data identitas akademik sebelum mengunggah berkas pengajuan magang BSI." />

<div id="flash-container">
    @if (session('warning'))
        <div class="flash" style="background:#FFFBEB; color:#92400E; border-color:#FDE68A;">
            <div style="display:flex; align-items:center; gap:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span>{{ session('warning') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
        </div>
    @endif

    @if (session('success'))
        <div class="flash">
            <div style="display:flex; align-items:center; gap:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="flash" style="background:var(--danger-bg); color:var(--danger); border-color:#efc0b9;">
            <div style="display:flex; align-items:center; gap:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    <strong>Terdapat kesalahan pengisian formulir :</strong>
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

{{-- Stepper Indikator --}}
<div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; margin-bottom:24px;">
    <div style="padding:14px 16px; background:#F0FDFA; border:2px solid var(--primary); border-radius:10px;">
        <div style="font-size:11px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:0.05em;">Tahap 1</div>
        <div style="font-size:14px; font-weight:700; color:var(--primary-dark); margin-top:2px;">Profil Akademik</div>
        <div style="font-size:11.5px; color:var(--ink-soft); margin-top:2px;">NIM, Kampus, Fakultas, Prodi, Semester</div>
    </div>

    <div style="padding:14px 16px; background:#F8FAFC; border:1px solid #CBD5E1; border-radius:10px; opacity:0.85;">
        <div style="font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.05em;">Tahap 2</div>
        <div style="font-size:14px; font-weight:700; color:var(--ink); margin-top:2px;">Unggah Dokumen Berkas</div>
        <div style="font-size:11.5px; color:var(--muted); margin-top:2px;">Proposal, Surat Pengantar, CV, Transkrip</div>
    </div>

    <div style="padding:14px 16px; background:#F8FAFC; border:1px solid #CBD5E1; border-radius:10px; opacity:0.65;">
        <div style="font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.05em;">Tahap 3</div>
        <div style="font-size:14px; font-weight:700; color:var(--ink); margin-top:2px;">Verifikasi Admin</div>
        <div style="font-size:11.5px; color:var(--muted); margin-top:2px;">Pemeriksaan dan Penetapan Cabang/Unit</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Formulir Kelengkapan Data Akademik</h3>
            <div class="card-sub">Pastikan data yang dimasukkan sesuai dengan identitas resmi kampus Anda.</div>
        </div>
    </div>

    <form action="{{ route('mahasiswa.profil.lengkapi.store') }}" method="POST">
        @csrf

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:18px;">
            <div>
                <label class="form-label">Nama Lengkap</label>
                <input type="text" value="{{ $user->name }}" disabled style="background:#F1F5F9; color:var(--ink);">
                <div style="font-size:11.5px; color:var(--muted); margin-top:4px;">Sesuai dengan nama akun yang Anda daftarkan.</div>
            </div>

            <div>
                <label class="form-label">Alamat Email</label>
                <input type="email" value="{{ $user->email }}" disabled style="background:#F1F5F9; color:var(--ink);">
                <div style="font-size:11.5px; color:var(--muted); margin-top:4px;">Email login akun Anda.</div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:18px;">
            <div>
                <label class="form-label">Nomor Induk Mahasiswa (NIM) <span style="color:var(--danger);">*</span></label>
                <input type="text" name="nim" value="{{ old('nim', $mahasiswa->nim ?? '') }}" placeholder="Contoh: 2111522001" required>
                @error('nim')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">Nomor WhatsApp Aktif <span style="color:var(--danger);">*</span></label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $mahasiswa->no_hp ?: $user->no_hp) }}" placeholder="Contoh: 081234567890" required>
                <div style="font-size:11.5px; color:var(--muted); margin-top:4px;">Untuk konfirmasi jadwal dan koordinasi mentor cabang.</div>
                @error('no_hp')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>

        <div style="margin-bottom:18px;">
            <label class="form-label">Perguruan Tinggi / Universitas <span style="color:var(--danger);">*</span></label>
            <input type="text" name="universitas" value="{{ old('universitas', $mahasiswa->universitas ?? '') }}" placeholder="Contoh: Universitas Gadjah Mada / UIN Sunan Kalijaga" required>
            @error('universitas')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:18px;">
            <div>
                <label class="form-label">Fakultas <span style="color:var(--danger);">*</span></label>
                <input type="text" name="fakultas" value="{{ old('fakultas', $mahasiswa->fakultas ?? '') }}" placeholder="Contoh: Fakultas Ekonomi & Bisnis Islam" required>
                @error('fakultas')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">Program Studi / Jurusan <span style="color:var(--danger);">*</span></label>
                <input type="text" name="jurusan" value="{{ old('jurusan', $mahasiswa->jurusan ?? '') }}" placeholder="Contoh: Perbankan Syariah / Sistem Informasi" required>
                @error('jurusan')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>

        <div style="margin-bottom:24px;">
            <label class="form-label">Semester Saat Ini <span style="color:var(--danger);">*</span></label>
            <input type="text" name="semester" value="{{ old('semester', $mahasiswa->semester ?? '') }}" placeholder="Contoh: Semester 6" required>
            <div style="font-size:11.5px; color:var(--muted); margin-top:4px;">Tingkat semester yang sedang Anda tempuh saat ini.</div>
            @error('semester')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--line); padding-top:20px;">
            <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-ghost">
                Kembali ke Dashboard
            </a>
            <button type="submit" class="btn btn-solid" style="padding:10px 24px; font-weight:700;">
                Simpan dan Lanjutkan ke Unggah Berkas
            </button>
        </div>
    </form>
</div>
@endsection
