@extends('layouts.app')

@section('title', 'SIMMM - Profil Saya')

@section('content')
@php
    $user = auth()->user();
    $mahasiswa = $mahasiswa ?? $user->mahasiswa;
    $userRole = $user->role ?? 'mahasiswa';
    $isAdmin = $userRole === 'admin';
    
    $statusPendaftaran = $mahasiswa->status_pendaftaran ?? 'belum_daftar';
    $statusMagang = $mahasiswa->status ?? 'non_aktif';
@endphp

<x-topbar title="Profil Akun Saya" :subtitle="$isAdmin ? 'Kelola informasi akun pengelola, identitas kontak, dan keamanan akun Admin/Mentor BSI.' : 'Kelola data diri, informasi magang, dan keamanan akun Anda di SIMMM BSI Area Yogyakarta.'">
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

    @if (session('warning'))
        <div class="flash" style="background:#FFFBEB; color:#92400E; border-color:#FDE68A;">
            <div style="display:flex; align-items:center; gap:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span>{{ session('warning') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="flash" style="background:var(--danger-bg); color:var(--danger); border-color:#efc0b9;">
            <div style="display:flex; align-items:center; gap:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    <strong>Terdapat kesalahan pada isian form :</strong>
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

<div class="grid-2" style="display:grid; grid-template-columns:1.25fr 1fr; gap:24px; align-items:start;">
    
    {{-- KOLOM KIRI: Biodata & Data Diri (Berdasarkan Role) --}}
    <div style="display:flex; flex-direction:column; gap:24px;">
        
        {{-- Card Header Ringkasan Profil --}}
        <div class="card">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:20px; border-bottom:1px solid var(--line); padding-bottom:20px;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:64px; height:64px; border-radius:50%; background:linear-gradient(135deg, var(--accent) 0%, #a67c2e 100%); display:flex; align-items:center; justify-content:center; font-family:'Fraunces',serif; font-size:24px; font-weight:700; color:var(--primary-dark); box-shadow:var(--shadow-md); flex-shrink:0;">
                        {{ collect(explode(' ', trim($user->name)))->filter()->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') ?: 'PG' }}
                    </div>
                    <div>
                        <h2 style="font-size:20px; font-weight:700; color:var(--primary-dark); margin:0;">{{ $user->name }}</h2>
                        <div style="display:flex; align-items:center; gap:8px; margin-top:4px; flex-wrap:wrap;">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-solid btn-sm" onclick="openEditProfilModal()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    <span>Edit Profil</span>
                </button>
            </div>

            @if($isAdmin)
                {{-- TAMPILAN PROFIL KHUSUS ADMIN / MENTOR --}}
                <div class="card-header" style="border-bottom:none; padding-bottom:0; margin-bottom:14px;">
                    <div>
                        <h3>Informasi Akun Admin/Mentor</h3>
                        <div class="card-sub">Data identitas diri dan jabatan pengelola di BSI Area Yogyakarta.</div>
                    </div>
                </div>

                <table style="width:100%; border-collapse:separate; border-spacing:0;">
                    <tbody>
                        <tr>
                            <td style="width:160px; color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:1px solid var(--line);">Nama Lengkap</td>
                            <td style="padding:10px 0; border-bottom:1px solid var(--line); font-weight:600; color:var(--ink);">
                                {{ $user->name }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:1px solid var(--line);">Alamat Email</td>
                            <td style="padding:10px 0; border-bottom:1px solid var(--line); font-weight:600; color:var(--primary-dark);">
                                {{ $user->email }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:1px solid var(--line);">Nomor HP / Whatsapp</td>
                            <td style="padding:10px 0; border-bottom:1px solid var(--line); font-weight:600; color:var(--primary-dark);">
                                {{ $user->no_hp ?: '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:1px solid var(--line);">Unit Kerja / Cabang</td>
                            <td style="padding:10px 0; border-bottom:1px solid var(--line); font-weight:600; color:var(--ink);">
                                Bank Syariah Indonesia Area Yogyakarta
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:1px solid var(--line);">Jabatan / Peran</td>
                            <td style="padding:10px 0; border-bottom:1px solid var(--line); color:var(--ink);">
                                {{ $user->jabatan ?? 'Admin SDI & Pembimbing Magang' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:none;">Tanggal Bergabung</td>
                            <td style="padding:10px 0; border-bottom:none; color:var(--ink);">
                                {{ $user->created_at ? $user->created_at->isoFormat('D MMMM Y') : '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            @else
                {{-- TAMPILAN PROFIL KHUSUS MAHASISWA --}}
                <div class="card-header" style="border-bottom:none; padding-bottom:0; margin-bottom:14px;">
                    <div>
                        <h3>Informasi Akun dan Biodata</h3>
                        <div class="card-sub">Data identitas pribadi dan perguruan tinggi mahasiswa.</div>
                    </div>
                </div>

                @php
                    $isAkademikLengkap = $mahasiswa && $mahasiswa->isProfilAkademikLengkap();
                @endphp

                @if(!$isAkademikLengkap)
                    <div style="background:#EFF6FF; border:1px solid #BFDBFE; border-radius:8px; padding:12px 16px; margin-bottom:16px; display:flex; align-items:center; justify-content:space-between; gap:12px;">
                        <div style="font-size:12.5px; color:#1E40AF; line-height:1.4;">
                            <strong>Profil Akademik Belum Lengkap :</strong> Silakan lengkapi NIM, Perguruan Tinggi, Fakultas, Program Studi, dan Semester untuk mengajukan pendaftaran magang.
                        </div>
                        <a href="{{ route('mahasiswa.profil.lengkapi') }}" class="btn btn-solid btn-sm" style="flex-shrink:0; font-size:12px;">
                            Lengkapi Sekarang
                        </a>
                    </div>
                @endif

                <table style="width:100%; border-collapse:separate; border-spacing:0;">
                    <tbody>
                        <tr>
                            <td style="width:160px; color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:1px solid var(--line);">Nama Lengkap</td>
                            <td style="padding:10px 0; border-bottom:1px solid var(--line); font-weight:600; color:var(--ink);">
                                {{ $user->name }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:1px solid var(--line);">Alamat Email</td>
                            <td style="padding:10px 0; border-bottom:1px solid var(--line); font-weight:600; color:var(--primary-dark);">
                                {{ $user->email }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:1px solid var(--line);">Nomor WhatsApp</td>
                            <td style="padding:10px 0; border-bottom:1px solid var(--line); font-weight:600; color:var(--primary-dark);">
                                @php
                                    $whatsapp = $mahasiswa?->no_hp ?: $user->no_hp;
                                @endphp
                                @if(!empty($whatsapp))
                                    {{ $whatsapp }}
                                @else
                                    <span style="color:var(--muted); font-style:italic;">Belum diisi</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:1px solid var(--line);">NIM</td>
                            <td style="padding:10px 0; border-bottom:1px solid var(--line); color:var(--ink);">
                                @if(!empty($mahasiswa?->nim))
                                    <span class="mono" style="font-weight:600;">{{ $mahasiswa->nim }}</span>
                                @else
                                    <span style="color:var(--muted); font-style:italic;">Belum diisi</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:1px solid var(--line);">Perguruan Tinggi</td>
                            <td style="padding:10px 0; border-bottom:1px solid var(--line); color:var(--ink);">
                                @if(!empty($mahasiswa?->universitas))
                                    <span style="font-weight:600;">{{ $mahasiswa->universitas }}</span>
                                @else
                                    <span style="color:var(--muted); font-style:italic;">Belum diisi</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:1px solid var(--line);">Fakultas</td>
                            <td style="padding:10px 0; border-bottom:1px solid var(--line); color:var(--ink);">
                                @if(!empty($mahasiswa?->fakultas))
                                    {{ $mahasiswa->fakultas }}
                                @else
                                    <span style="color:var(--muted); font-style:italic;">Belum diisi</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:1px solid var(--line);">Program Studi</td>
                            <td style="padding:10px 0; border-bottom:1px solid var(--line); color:var(--ink);">
                                @if(!empty($mahasiswa?->jurusan))
                                    <span style="font-weight:600;">{{ $mahasiswa->jurusan }}</span>
                                @else
                                    <span style="color:var(--muted); font-style:italic;">Belum diisi</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="color:var(--muted); font-weight:600; padding:10px 12px 10px 0; border-bottom:none;">Semester Saat Ini</td>
                            <td style="padding:10px 0; border-bottom:none; color:var(--ink);">
                                @if(!empty($mahasiswa?->semester))
                                    {{ $mahasiswa->semester }}
                                @else
                                    <span style="color:var(--muted); font-style:italic;">Belum diisi</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Card Ganti Password --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h3>Keamanan dan Kata Sandi</h3>
                    <div class="card-sub">Perbarui kata sandi akun Anda secara berkala untuk keamanan.</div>
                </div>
            </div>

            <form action="{{ route('profil.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div style="margin-bottom:16px;">
                    <label class="form-label">Kata Sandi Saat Ini <span style="color:var(--danger);">*</span></label>
                    <input type="password" name="current_password" placeholder="Masukkan kata sandi lama Anda" required>
                    @error('current_password')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
                    <div>
                        <label class="form-label">Kata Sandi Baru <span style="color:var(--danger);">*</span></label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                        @error('password')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label">Konfirmasi Kata Sandi Baru <span style="color:var(--danger);">*</span></label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi baru" required minlength="6">
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end;">
                    <button type="submit" class="btn btn-solid">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span>Perbarui Kata Sandi</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- KOLOM KANAN: Berdasarkan Role --}}
    <div style="display:flex; flex-direction:column; gap:24px;">

        @if($isAdmin)
            {{-- Card Ringkasan Tugas & Pengelolaan untuk Admin/Mentor --}}
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3>Ringkasan Tugas dan Pengelolaan</h3>
                        <div class="card-sub">Statistik pemantauan pendaftaran dan mahasiswa bimbingan aktif.</div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div style="padding:16px; background:var(--bg); border:1px solid var(--line); border-radius:10px;">
                        <div style="font-size:11.5px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Pendaftar Baru</div>
                        <div style="font-size:26px; font-weight:700; color:var(--primary-dark); display:flex; align-items:baseline; gap:8px;">
                            {{ $stats['pendaftar_menunggu'] ?? 0 }}
                            <span style="font-size:12px; color:var(--muted); font-weight:400;">berkas</span>
                        </div>
                        <div style="font-size:11.5px; color:var(--ink-soft); margin-top:4px;">Menunggu verifikasi cabang</div>
                    </div>

                    <div style="padding:16px; background:var(--bg); border:1px solid var(--line); border-radius:10px;">
                        <div style="font-size:11.5px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Mahasiswa Aktif</div>
                        <div style="font-size:26px; font-weight:700; color:var(--green); display:flex; align-items:baseline; gap:8px;">
                            {{ $stats['mahasiswa_aktif'] ?? 0 }}
                            <span style="font-size:12px; color:var(--muted); font-weight:400;">orang</span>
                        </div>
                        <div style="font-size:11.5px; color:var(--ink-soft); margin-top:4px;">Sedang melaksanakan magang</div>
                    </div>

                    <div style="padding:16px; background:var(--bg); border:1px solid var(--line); border-radius:10px;">
                        <div style="font-size:11.5px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Laporan Akhir</div>
                        <div style="font-size:26px; font-weight:700; color:var(--amber); display:flex; align-items:baseline; gap:8px;">
                            {{ $stats['laporan_menunggu'] ?? 0 }}
                            <span style="font-size:12px; color:var(--muted); font-weight:400;">dokumen</span>
                        </div>
                        <div style="font-size:11.5px; color:var(--ink-soft); margin-top:4px;">Perlu verifikasi mentor</div>
                    </div>

                    <div style="padding:16px; background:var(--bg); border:1px solid var(--line); border-radius:10px;">
                        <div style="font-size:11.5px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Total Mahasiswa</div>
                        <div style="font-size:26px; font-weight:700; color:var(--ink); display:flex; align-items:baseline; gap:8px;">
                            {{ $stats['total_mahasiswa'] ?? 0 }}
                            <span style="font-size:12px; color:var(--muted); font-weight:400;">total</span>
                        </div>
                        <div style="font-size:11.5px; color:var(--ink-soft); margin-top:4px;">Terdaftar di SIMMM BSI</div>
                    </div>
                </div>

                <div style="margin-top:16px; padding-top:14px; border-top:1px solid var(--line); display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:12.5px; color:var(--muted);">Menu Aksi Cepat Admin:</span>
                    <div style="display:flex; gap:8px;">
                        <a href="{{ route('mahasiswas.index') }}" class="btn btn-ghost btn-sm">Verifikasi Berkas</a>
                        <a href="{{ route('laporan.index') }}" class="btn btn-ghost btn-sm">Verifikasi Laporan</a>
                    </div>
                </div>
            </div>
        @else
            {{-- Card Informasi Magang untuk Mahasiswa --}}
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3>Informasi Magang</h3>
                        <div class="card-sub">Detail penempatan dan pembimbingan di BSI Area Yogyakarta.</div>
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div style="padding:14px 16px; background:var(--bg); border:1px solid var(--line); border-radius:8px;">
                        <div style="font-size:11.5px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Unit Kerja / Kantor</div>
                        <div style="font-size:14px; font-weight:700; color:var(--primary-dark); display:flex; align-items:center; gap:8px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--accent);"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            BSI Area Yogyakarta
                        </div>
                    </div>

                    <div style="padding:14px 16px; background:var(--bg); border:1px solid var(--line); border-radius:8px;">
                        <div style="font-size:11.5px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Cabang/Unit Penempatan</div>
                        <div style="font-size:14px; font-weight:700; color:var(--ink);">
                            {{ $mahasiswa?->cabang?->nama ?? ($statusPendaftaran === 'belum_daftar' ? 'Belum Ditempatkan (Belum Mengajukan Magang)' : 'Belum Ditempatkan (Proses Pendaftaran)') }}
                        </div>
                    </div>

                    <div style="padding:14px 16px; background:var(--bg); border:1px solid var(--line); border-radius:8px;">
                        <div style="font-size:11.5px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Mentor / Pembimbing Cabang</div>
                        <div style="font-size:14px; font-weight:700; color:var(--ink); display:flex; align-items:center; gap:8px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--primary);"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            {{ $mahasiswa?->nama_mentor ?? ($statusPendaftaran === 'belum_daftar' ? 'Belum Ditentukan' : 'Tim Mentor BSI Area Yogyakarta') }}
                        </div>
                    </div>

                    <div style="padding:14px 16px; background:var(--bg); border:1px solid var(--line); border-radius:8px;">
                        <div style="font-size:11.5px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Periode Pelaksanaan Magang</div>
                        <div style="font-size:14px; font-weight:700; color:var(--ink); display:flex; align-items:center; gap:8px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--primary);"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            @if($mahasiswa && $mahasiswa->tanggal_mulai)
                                {{ \Illuminate\Support\Carbon::parse($mahasiswa->tanggal_mulai)->isoFormat('D MMMM Y') }}
                                @if($mahasiswa->tanggal_selesai)
                                    &mdash; {{ \Illuminate\Support\Carbon::parse($mahasiswa->tanggal_selesai)->isoFormat('D MMMM Y') }}
                                @else
                                    &mdash; Selesai
                                @endif
                            @else
                                <span style="font-weight:400; color:var(--muted); font-style:italic;">Belum ditentukan</span>
                            @endif
                        </div>
                    </div>

                    <div style="padding:14px 16px; background:var(--bg); border:1px solid var(--line); border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-size:11.5px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Status Magang</div>
                            <div style="font-size:13px; font-weight:600; color:var(--ink-soft); margin-top:2px;">
                                @if($statusMagang === 'selesai')
                                    Program Telah Selesai
                                @elseif($statusPendaftaran === 'ditempatkan')
                                    Magang Berjalan Aktif
                                @elseif($statusPendaftaran === 'belum_daftar')
                                    Belum Mengajukan Magang
                                @else
                                    Dalam Proses Verifikasi
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Catatan Ketentuan Data Khusus Mahasiswa --}}
            <div style="background:var(--amber-bg); border:1px solid #e5cc9c; border-radius:10px; padding:18px; display:flex; gap:14px; align-items:flex-start;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--amber); flex-shrink:0; margin-top:2px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <div style="font-size:12.5px; line-height:1.5; color:var(--ink-soft);">
                    <div style="font-weight:700; color:var(--amber); font-size:13.5px; margin-bottom:3px;">Catatan Perubahan Data Akademik :</div>
                    Perubahan data vital seperti NIM, Nama Lengkap, Perguruan Tinggi, dan Program Studi hanya dapat disesuaikan melalui pengajuan ke <strong>Admin / Mentor BSI Area Yogyakarta</strong>.
                </div>
            </div>
        @endif

    </div>
</div>

{{-- Modal Edit Profil --}}
<div id="edit-profil-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:540px;" role="dialog" aria-modal="true" aria-labelledby="modal-edit-profil-title">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--line); padding-bottom:14px;">
            <div>
                <div id="modal-edit-profil-title" class="modal-title" style="margin:0; font-size:18px; font-weight:700; color:var(--primary-dark);">Edit Profil Saya</div>
                <div style="font-size:12px; color:var(--muted); margin-top:2px;">Ubah informasi kontak dan identitas profil Anda.</div>
            </div>
            <button type="button" onclick="closeEditProfilModal()" aria-label="Tutup modal" style="background:none; border:none; cursor:pointer; color:var(--muted); font-size:24px; line-height:1; padding:0 4px;">&times;</button>
        </div>

        <form action="{{ route('profil.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom:14px;">
                <label class="form-label">Nama Lengkap <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required placeholder="Nama lengkap Anda">
            </div>

            <div style="margin-bottom:14px;">
                <label class="form-label">Alamat Email <span style="color:var(--danger);">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Contoh: user@email.com">
            </div>

            <div style="margin-bottom:14px;">
                <label class="form-label">Nomor WhatsApp Aktif</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $isAdmin ? ($user->no_hp ?? '') : ($mahasiswa?->no_hp ?: $user->no_hp)) }}" placeholder="08xxxxxxxxxx">
            </div>

            @if($isAdmin)
                <div style="margin-bottom:20px;">
                    <label class="form-label">Jabatan / Unit Kerja</label>
                    <input type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan ?? 'Admin SDI & Pembimbing Magang') }}" placeholder="Contoh: Admin SDI & Pembimbing Magang">
                </div>
            @else
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                    <div>
                        <label class="form-label">Nomor Induk Mahasiswa (NIM)</label>
                        <input type="text" name="nim" value="{{ old('nim', $mahasiswa?->nim ?? '') }}" placeholder="Contoh: 2111522001">
                    </div>
                    <div>
                        <label class="form-label">Semester Saat Ini</label>
                        <input type="text" name="semester" value="{{ old('semester', $mahasiswa?->semester ?? '') }}" placeholder="Contoh: Semester 6">
                    </div>
                </div>
                <div style="margin-bottom:14px;">
                    <label class="form-label">Perguruan Tinggi / Universitas</label>
                    <input type="text" name="universitas" value="{{ old('universitas', $mahasiswa?->universitas ?? '') }}" placeholder="Nama Universitas / Institut">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:20px;">
                    <div>
                        <label class="form-label">Fakultas</label>
                        <input type="text" name="fakultas" value="{{ old('fakultas', $mahasiswa?->fakultas ?? '') }}" placeholder="Contoh: Fakultas Ekonomi & Bisnis Islam">
                    </div>
                    <div>
                        <label class="form-label">Program Studi / Jurusan</label>
                        <input type="text" name="jurusan" value="{{ old('jurusan', $mahasiswa?->jurusan ?? '') }}" placeholder="Contoh: Perbankan Syariah">
                    </div>
                </div>
            @endif

            <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--line); padding-top:16px;">
                <button type="button" class="btn btn-ghost" onclick="closeEditProfilModal()">Batal</button>
                <button type="submit" class="btn btn-solid">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const editProfilModal = document.getElementById('edit-profil-modal');

    function openEditProfilModal() {
        if (!editProfilModal) return;
        editProfilModal.classList.add('active');
        editProfilModal.setAttribute('aria-hidden', 'false');
    }

    function closeEditProfilModal() {
        if (!editProfilModal) return;
        editProfilModal.classList.remove('active');
        editProfilModal.setAttribute('aria-hidden', 'true');
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (editProfilModal) {
            editProfilModal.addEventListener('click', (e) => {
                if (e.target === editProfilModal) closeEditProfilModal();
            });
        }
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' || e.key === 'Esc') {
                if (editProfilModal && editProfilModal.classList.contains('active')) closeEditProfilModal();
            }
        });
    });
</script>
@endsection
