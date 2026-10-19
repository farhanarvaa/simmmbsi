@extends('layouts.app')

@section('title', 'SIMMM - Dashboard Mahasiswa')

@section('content')
@php
    $mahasiswa = auth()->user()->mahasiswa;
    $statusPendaftaran = $mahasiswa->status_pendaftaran ?? 'belum_daftar';
    $statusPeserta = $mahasiswa->status ?? 'non_aktif';

    // Tahapan Program Magang:
    // 1: Ajukan Proposal | 2: Verifikasi Berkas | 3: Penempatan Cabang/Unit | 4: Pelaksanaan Magang | 5: Penilaian Akhir | 6: Kelulusan & Sertifikat
    $currentStep = 1;

    if ($statusPendaftaran === 'diajukan') {
        $currentStep = 2;
    } elseif ($statusPendaftaran === 'diterima') {
        $currentStep = 3;
    } elseif ($statusPendaftaran === 'ditempatkan') {
        $currentStep = 4;
    }

    if ($mahasiswa && $mahasiswa->penilaian) {
        $currentStep = 5;
    }

    if ($statusPeserta === 'selesai') {
        $currentStep = 6;
    }

    // Hitung persentase berdasarkan rasio stepper proporsional
    $progressPercent = round(($currentStep / 6) * 100);

    // Untuk mahasiswa yang sedang magang aktif, hitung progres hari kerja riil
    $hariKeMagang = null;
    $totalHariMagang = null;
    $progressHariKerja = null;

    if ($currentStep === 4 && $mahasiswa && $mahasiswa->tanggal_mulai) {
        $mulai = \Carbon\Carbon::parse($mahasiswa->tanggal_mulai);
        $sekarang = now();
        $hariKeMagang = max(1, $mulai->diffInWeekdays($sekarang) + 1);

        if ($mahasiswa->tanggal_selesai) {
            $selesai = \Carbon\Carbon::parse($mahasiswa->tanggal_selesai);
            $totalHariMagang = max(1, $mulai->diffInWeekdays($selesai) + 1);
            $progressHariKerja = min(100, round(($hariKeMagang / $totalHariMagang) * 100));
        }
    }

    $steps = [
        1 => ['label' => 'Pengajuan Proposal', 'sub' => 'Pengunggahan dokumen proposal magang dan surat pengantar dari kampus.'],
        2 => ['label' => 'Proses Verifikasi', 'sub' => 'Peninjauan berkas dan proses verifikasi admin.'],
        3 => ['label' => 'Penempatan', 'sub' => 'Penetapan cabang/unit dan mentor pembimbing.'],
        4 => ['label' => 'Pelaksanaan Magang', 'sub' => 'Pengisian presensi dan logbook harian.'],
        5 => ['label' => 'Penilaian', 'sub' => 'Pemberian penilaian lapangan dari Admin/Mentor.'],
        6 => ['label' => 'Kelulusan', 'sub' => 'Penerbitan surat keterangan selesai magang setelah nilai lengkap.']
    ];
@endphp

{{-- Header Halaman --}}
<div class="topbar">
    <div>
        <h1>Dashboard Mahasiswa</h1>
        <div class="subtitle">Portal administrasi dan aktivitas magang Bank Syariah Indonesia Area Yogyakarta.</div>
    </div>
</div>

{{-- Alert Notifikasi Session --}}
@if (session('success'))
    <div class="flash">
        <div style="display:flex; align-items:center; gap:10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:18px; line-height:1;">&times;</button>
    </div>
@endif

@if (session('warning'))
    <div class="flash" style="background:#FFFBEB; color:#92400E; border-color:#FDE68A;">
        <div style="display:flex; align-items:center; gap:10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <span>{{ session('warning') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:18px; line-height:1;">&times;</button>
    </div>
@endif

@if (session('error'))
    <div class="flash" style="background:var(--danger-bg); color:var(--danger); border-color:#fca5a5;">
        <div style="display:flex; align-items:center; gap:10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:18px; line-height:1;">&times;</button>
    </div>
@endif

{{-- Catatan Revisi dari Pembimbing/Admin --}}
@if($mahasiswa && $statusPendaftaran === 'revisi' && $mahasiswa->catatan_revisi)
    <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:10px; padding:18px 20px; margin-bottom:24px; display:flex; gap:14px; align-items:flex-start;">
        <div style="width:36px; height:36px; border-radius:8px; background:#FEF3C7; display:flex; align-items:center; justify-content:center; color:#B45309; flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div>
            <div style="font-weight:700; color:#92400E; font-size:14px; margin-bottom:4px;">Catatan Revisi dari Admin / Mentor:</div>
            <div style="font-size:13.5px; color:#78350F; line-height:1.5;">{{ $mahasiswa->catatan_revisi }}</div>
            <div style="font-size:12px; color:#B45309; margin-top:8px;">Silakan perbarui data atau dokumen Anda pada formulir pendaftaran di bawah ini lalu kirim kembali.</div>
        </div>
    </div>
@endif



{{-- ============================================================ --}}
{{-- GRID UTAMA: Alur Program Magang + Informasi & Menu Utama --}}
{{-- ============================================================ --}}
<div style="display:flex; flex-direction:column; gap:24px; margin-bottom:24px;">
    {{-- Card 1: Pelacak Progres Program Magang --}}
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <div>
                <h3>Alur Program Magang</h3>
                <div class="card-sub">Status tahapan dari seleksi awal hingga penerbitan surat keterangan selesai magang.</div>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                @if($hariKeMagang && $totalHariMagang)
                    <span style="font-size:11.5px; color:var(--ink-soft); font-weight:500;">Hari ke-{{ $hariKeMagang }} dari {{ $totalHariMagang }}</span>
                @endif
                <div style="font-size:12px; font-weight:700; color:var(--primary); background:var(--primary-light); padding:4px 10px; border-radius:6px;">
                    Tahap {{ $currentStep }}/6
                </div>
            </div>
        </div>

        {{-- Progress Bar Minimalis --}}
       

        {{-- List Step Tracker bergaya Alur Program pada halaman awal --}}
        <div style="display:grid; grid-template-columns:repeat(6, minmax(0, 1fr)); gap:0; margin:8px 0 4px; border-top:1px dashed #F3D9B5;">
            @foreach($steps as $num => $step)
                @php
                    $isPassed = $currentStep > $num;
                    $isCurrent = $currentStep === $num;
                @endphp
                <div style="position:relative; padding:0 10px;">
                    <div style="width:38px; height:38px; margin:-20px auto 14px; border:1px solid {{ $isCurrent ? 'var(--primary)' : ($isPassed ? 'var(--green)' : '#CBD5E1') }}; border-radius:50%; background:{{ $isCurrent ? '#F0FDFA' : ($isPassed ? '#F0FDF4' : '#FFFFFF') }}; color:{{ $isCurrent ? 'var(--primary)' : ($isPassed ? 'var(--green)' : 'var(--muted)') }}; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; position:relative; z-index:1;">
                        0{{ $num }}
                    </div>
                    <div style="font-size:13px; font-weight:700; color:{{ $isCurrent ? 'var(--primary-dark)' : ($isPassed ? 'var(--ink)' : 'var(--muted)') }}; line-height:1.25;">{{ $step['label'] }}</div>
                    <div style="font-size:11px; color:var(--muted); margin-top:4px; line-height:1.35;">{{ $step['sub'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- Status Action Banner / Ringkasan Kondisi Terkini --}}
        @if($statusPeserta === 'selesai')
            <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 18px; background:#F0FDF4; border-radius:8px; margin-top:20px; border:1px solid #BBF7D0;">
                <div>
                    <div style="font-size:13.5px; color:#166534; font-weight:700;">Program Magang Telah Selesai dan Lulus</div>
                    <div style="font-size:12px; color:#15803D; margin-top:2px;">Selamat! Seluruh rangkaian kegiatan dan evaluasi magang telah rampung.</div>
                </div>
                <div style="display:flex; gap:8px; flex-shrink:0;">
                    <a href="{{ route('sertifikasi.index') }}" class="btn btn-solid btn-sm">Sertifikasi</a>
                    <a href="{{ route('penilaian.index') }}" class="btn btn-ghost btn-sm">Hasil Penilaian</a>
                </div>
            </div>
        @elseif($statusPendaftaran === 'diterima')
            <div style="padding:16px 18px; background:#F0FDF4; border-radius:8px; margin-top:20px; border:1px solid #BBF7D0; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                <div>
                    <div style="font-size:13.5px; color:#166534; font-weight:700;">Selamat! Pendaftaran Magang Anda Diterima</div>
                    <div style="font-size:12px; color:#15803D; margin-top:2px;">Surat Penerimaan Magang resmi telah diterbitkan. Silakan unduh PDF untuk dicetak dan dimintakan tanda tangan.</div>
                </div>
                <div style="display:flex; gap:8px; flex-shrink:0;">
                    <a href="{{ route('penempatan.index') }}" class="btn btn-solid btn-sm">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Surat Penerimaan Magang
                    </a>
                </div>
            </div>
        @elseif($statusPendaftaran === 'ditempatkan')
            @php
                $presensiHariIni = \App\Models\Presensi::where('user_id', auth()->id())
                    ->whereDate('tanggal', now()->toDateString())
                    ->first();
            @endphp

            @if(!$presensiHariIni)
                <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 18px; background:#F0FDFA; border-radius:8px; margin-top:20px; border:1px solid #99F6E4;">
                    <div>
                        <div style="font-size:13.5px; color:#0F766E; font-weight:700;">Presensi Hari Ini : Belum Masuk</div>
                        <div style="font-size:12px; color:#115E59; margin-top:2px;">Jangan lupa catat kehadiranmu hari ini </div>
                    </div>
                    <a href="{{ route('presensis.index', ['action' => 'create']) }}" class="btn btn-solid btn-sm">Presensi Masuk</a>
                </div>
            @elseif($presensiHariIni->jam_masuk && !$presensiHariIni->jam_pulang)
                <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 18px; background:#FFFBEB; border-radius:8px; margin-top:20px; border:1px solid #FDE68A;">
                    <div>
                        <div style="font-size:13.5px; color:#92400E; font-weight:700;">Presensi Hari Ini : Sudah Masuk, Belum Pulang</div>
                        <div style="font-size:12px; color:#78350F; margin-top:2px;">Presensi Masuk tercatat pukul {{ \Illuminate\Support\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') }} WIB.</div>
                        <div style="font-size:12px; color:#78350F; margin-top:2px;">Status tercatat hadir. Silakan lakukan Presensi Pulang saat selesai.</div>
                    </div>
                    <a href="{{ route('presensis.index', ['action' => 'create']) }}" class="btn btn-solid btn-sm" style="background:#D97706; border-color:#D97706;">Presensi Pulang</a>
                </div>
            @else
                <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 18px; background:#F0FDF4; border-radius:8px; margin-top:20px; border:1px solid #BBF7D0;">
                    <div>
                        <div style="font-size:13.5px; color:#166534; font-weight:700;">Presensi Hari Ini : Selesai</div>
                        <div style="font-size:12px; color:#15803D; margin-top:2px;">
                            @if($presensiHariIni->jam_masuk && $presensiHariIni->jam_pulang)
                                Masuk: {{ \Illuminate\Support\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') }} WIB &bull; Pulang : {{ \Illuminate\Support\Carbon::parse($presensiHariIni->jam_pulang)->format('H:i') }} WIB
                            @else
                                Status: {{ strtoupper($presensiHariIni->status) }} &mdash; {{ $presensiHariIni->catatan ?? 'Tercatat' }}
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('presensis.index') }}" class="btn btn-ghost btn-sm">Riwayat Presensi</a>
                </div>
            @endif
        @else
            <div style="padding:14px 16px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; margin-top:20px; font-size:13px; color:var(--ink-soft); display:flex; align-items:center; gap:12px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--primary); flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <div>
                    @if(!$mahasiswa || $statusPendaftaran === 'belum_daftar')
                        Anda belum mengajukan pendaftaran magang. Silakan lengkapi data akademik dan upload berkas di bawah ini untuk memulai.
                    @elseif($statusPendaftaran === 'diajukan')
                        Berkas Anda telah tersimpan dan sedang dalam antrean verifikasi Admin/Mentor.
                    @elseif($statusPendaftaran === 'ditolak')
                        Pendaftaran magang periode ini belum disetujui. Silakan hubungi admin jika memerlukan informasi.
                    @endif
                </div>
            </div>
        @endif
    </div>

   

{{-- ============================================================ --}}
{{-- BAGIAN PENGAJUAN PENDAFTARAN MAGANG (POSISI DI BAWAH)        --}}
{{-- ============================================================ --}}
@if(!$mahasiswa || $statusPendaftaran === 'belum_daftar' || $statusPendaftaran === 'revisi')
    {{-- Card Besar Tahap 1 & Tahap 2 saat belum daftar atau revisi --}}
    <div class="card" style="border:1.5px solid {{ $statusPendaftaran === 'revisi' ? '#FDE68A' : 'var(--primary-light)' }}; margin-top:24px; margin-bottom:0;">
        <div class="card-header" style="flex-wrap:wrap; gap:12px;">
            <div>
                <h3 style="color:{{ $statusPendaftaran === 'revisi' ? '#92400E' : 'var(--primary-dark)' }};">Pengajuan Pendaftaran Magang</h3>
                <div class="card-sub">
                    @if($statusPendaftaran === 'revisi')
                        Perbaiki dan perbarui dokumen pendaftaran magang Anda sesuai catatan pembimbing / admin.
                    @else
                        Lengkapi 2 tahapan pendaftaran di bawah ini untuk memulai proses pengajuan magang BSI Area Yogyakarta.
                    @endif
                </div>
            </div>
            @if($statusPendaftaran === 'revisi')
                <a href="{{ route('mahasiswa.pendaftaran.berkas') }}" class="btn btn-solid btn-sm" style="font-weight:700; background:#B45309; border-color:#B45309;">
                    Unggah Ulang Berkas 
                </a>
            @else
                <a href="{{ route('mahasiswa.pendaftaran.ajukan') }}" class="btn btn-solid btn-sm" style="font-weight:700;">
                    Ajukan Pendaftaran Magang 
                </a>
            @endif
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:8px;">
            {{-- Tahap 1: Profil Akademik --}}
            @php
                $isAkademikLengkap = $mahasiswa && $mahasiswa->isProfilAkademikLengkap();
            @endphp
            <div style="border:1px solid {{ $isAkademikLengkap ? '#BBF7D0' : '#FDE68A' }}; background:{{ $isAkademikLengkap ? '#F0FDF4' : '#FFFBEB' }}; border-radius:10px; padding:18px; display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:{{ $isAkademikLengkap ? '#166534' : '#92400E' }};">
                            Tahap 1 
                        </span>
                    </div>
                    <div style="font-size:14.5px; font-weight:700; color:var(--ink);">Data Identitas Kampus</div>
                    <div style="font-size:12.5px; color:var(--ink-soft); margin-top:4px; line-height:1.4;">
                        @if($isAkademikLengkap)
                            NIM : <strong>{{ $mahasiswa->nim }}</strong> &bull; {{ $mahasiswa->universitas }} ({{ $mahasiswa->jurusan }})
                        @else
                            Wajib melengkapi NIM, Perguruan Tinggi, Fakultas, Program Studi, dan Semester aktif.
                        @endif
                    </div>
                </div>
                <div style="margin-top:16px;">
                    <a href="{{ route('mahasiswa.profil.lengkapi') }}" class="btn btn-sm {{ $isAkademikLengkap ? 'btn-ghost' : 'btn-solid' }}" style="font-size:12px;">
                        {{ $isAkademikLengkap ? 'Ubah Profil Akademik' : 'Lengkapi Profil Sekarang' }}
                    </a>
                </div>
            </div>

            {{-- Tahap 2: Unggah Berkas Persyaratan --}}
            <div style="border:1px solid {{ $isAkademikLengkap ? 'var(--line)' : '#E2E8F0' }}; background:{{ $isAkademikLengkap ? '#FFFFFF' : '#F8FAFC' }}; border-radius:10px; padding:18px; display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:var(--muted);">
                            Tahap 2 
                        </span>
                    </div>
                    <div style="font-size:14.5px; font-weight:700; color:var(--ink);">Dokumen Persyaratan Magang</div>
                    <div style="font-size:12.5px; color:var(--ink-soft); margin-top:4px; line-height:1.4;">
                        Unggah proposal magang resmi kampus, surat pengantar fakultas/prodi, CV, dan transkrip nilai (format PDF).
                    </div>
                </div>
                <div style="margin-top:16px;">
                    <a href="{{ route('mahasiswa.pendaftaran.ajukan') }}" class="btn btn-solid btn-sm" style="font-size:12px;">
                        Unggah Berkas Persyaratan
                    </a>
                </div>
            </div>
        </div>
    </div>
@elseif($statusPendaftaran === 'diajukan')
    {{-- Status Ringkas: Diajukan / Menunggu Verifikasi (Satu baris status ringkas) --}}
    @php
        $tglPengajuan = ($mahasiswa->updated_at ?? $mahasiswa->created_at);
    @endphp
    <div class="card" style="margin-top:24px; margin-bottom:0; padding:18px 24px; border:1px solid #BAE6FD; background:#F0F9FF; box-shadow:var(--shadow-sm);">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="width:44px; height:44px; border-radius:10px; background:#DBEAFE; color:#2563EB; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <span style="font-size:15px; font-weight:700; color:#1E40AF;">Pengajuan Pendaftaran Magang: Menunggu Verifikasi</span>
                        <span class="badge badge-wait" style="font-size:11px; padding:2px 8px;">Diajukan</span>
                    </div>
                    <div style="font-size:12.5px; color:#2563EB; margin-top:4px; display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                        <span>Tahap 1 (Profil Akademik) &amp; Tahap 2 (Dokumen Berkas) selesai diisi. Berkas sedang dalam antrean verifikasi Admin.</span>
                        @if($tglPengajuan)
                            <span style="display:inline-flex; align-items:center; gap:5px; font-weight:600; color:#1D4ED8; background:#DBEAFE; padding:2px 8px; border-radius:4px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Tanggal Pengajuan: {{ $tglPengajuan->locale('id')->translatedFormat('d F Y, H:i') }} WIB
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
                <a href="{{ route('mahasiswa.pendaftaran.berkas') }}" class="btn btn-ghost btn-sm" style="font-size:12.5px; border-color:#93C5FD; color:#1D4ED8; background:#FFFFFF;">
                    Lihat Berkas Pendaftaran &rarr;
                </a>
            </div>
        </div>
    </div>
@elseif(in_array($statusPendaftaran, ['diterima', 'ditempatkan']))
    {{-- Status Ringkas: Diterima & Informasi Penempatan (Satu baris status ringkas) --}}
    <div class="card" style="margin-top:24px; margin-bottom:0; padding:18px 24px; border:1px solid #BBF7D0; background:#F0FDF4; box-shadow:var(--shadow-sm);">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="width:44px; height:44px; border-radius:10px; background:#DCFCE7; color:#15803D; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div>
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <span style="font-size:15px; font-weight:700; color:#166534;">Pendaftaran Magang Diterima </span>
                    </div>
                    <div style="font-size:13px; color:#14532D; margin-top:4px; display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                        <span>
                            Cabang/Unit Penempatan : <strong>{{ $mahasiswa->cabang?->nama ?: 'Menunggu konfirmasi admin' }}</strong>
                        </span>
                        
                        @if($mahasiswa->tanggal_mulai && $mahasiswa->tanggal_selesai)
                            <span style="color:#86EFAC;">&bull;</span>
                            <span style="display:inline-flex; align-items:center; gap:5px; font-weight:600; color:#15803D; background:#DCFCE7; padding:2px 8px; border-radius:4px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Periode: {{ \Carbon\Carbon::parse($mahasiswa->tanggal_mulai)->locale('id')->translatedFormat('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($mahasiswa->tanggal_selesai)->locale('id')->translatedFormat('d M Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
                <a href="{{ route('penempatan.index') }}" class="btn btn-solid btn-sm" style="font-size:12.5px; background:var(--primary); border-color:var(--primary);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Surat Penerimaan Magang
                </a>
            </div>
        </div>
    </div>
@elseif($statusPendaftaran === 'ditolak')
    <div class="card" style="margin-top:24px; margin-bottom:0; padding:18px 24px; border:1px solid #FECACA; background:#FEF2F2;">
        <div style="display:flex; align-items:center; gap:16px;">
            <div style="width:44px; height:44px; border-radius:10px; background:#FEE2E2; color:#DC2626; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div style="font-size:15px; font-weight:700; color:#991B1B;">Pendaftaran Magang Tidak Disetujui</div>
                <div style="font-size:12.5px; color:#B91C1C; margin-top:4px;">
                    {{ $mahasiswa->catatan_revisi ?: 'Mohon maaf, pendaftaran Anda pada periode ini belum dapat diterima. Silakan hubungi admin cabang untuk informasi lebih lanjut.' }}
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
