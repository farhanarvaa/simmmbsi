@extends('layouts.app')

@section('title', 'SIMMM - Unggah Berkas Persyaratan Magang')

@section('content')
<div class="topbar">
    <div>
        <h1>Unggah Berkas Persyaratan Magang</h1>
        <div class="subtitle">Tahap 2 dari 3 : Unggah dokumen persyaratan magang resmi Bank Syariah Indonesia Area Yogyakarta.</div>
    </div>
</div>

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
                    <strong>Terdapat kesalahan pada berkas yang diunggah :</strong>
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
    <div style="padding:14px 16px; background:#F0FDF4; border:1px solid #BBF7D0; border-radius:10px;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:11px; font-weight:700; color:var(--green); text-transform:uppercase; letter-spacing:0.05em;">Tahap 1</div>
            <span style="font-size:12px; color:var(--green); font-weight:700;">&#10003;</span>
        </div>
        <div style="font-size:14px; font-weight:700; color:#166534; margin-top:2px;">Profil Akademik</div>
        <div style="font-size:11.5px; color:#15803D; margin-top:2px;">Data akademik lengkap dan terverifikasi</div>
    </div>

    <div style="padding:14px 16px; background:#F0FDFA; border:2px solid var(--primary); border-radius:10px;">
        <div style="font-size:11px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:0.05em;">Tahap 2 </div>
        <div style="font-size:14px; font-weight:700; color:var(--primary-dark); margin-top:2px;">Unggah Dokumen Berkas</div>
        <div style="font-size:11.5px; color:var(--ink-soft); margin-top:2px;">Proposal, Surat Pengantar, CV, Transkrip</div>
    </div>

    <div style="padding:14px 16px; background:#F8FAFC; border:1px solid #CBD5E1; border-radius:10px; opacity:0.65;">
        <div style="font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.05em;">Tahap 3</div>
        <div style="font-size:14px; font-weight:700; color:var(--ink); margin-top:2px;">Verifikasi Admin</div>
        <div style="font-size:11.5px; color:var(--muted); margin-top:2px;">Pemeriksaan dan Penetapan Cabang/Unit</div>
    </div>
</div>

{{-- Pratinjau Data Akademik dan Berkas Sebelum Pengajuan --}}
<div class="card" style="margin-bottom:24px; border:1px solid var(--line);">
    <div class="card-header">
        <div>
            <h3>Pratinjau Data dan Berkas Pengajuan</h3>
            <div class="card-sub">Periksa kembali seluruh informasi berikut sebelum mengirim pengajuan pendaftaran magang.</div>
        </div>
        <a href="{{ route('mahasiswa.profil.lengkapi') }}" class="btn btn-ghost btn-sm">Ubah Data Profil</a>
    </div>

    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; margin-bottom:20px;">
        <div style="padding:12px 14px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px;">
            <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">Nama Lengkap</div>
            <div style="font-size:13.5px; color:var(--ink); font-weight:600; margin-top:4px;">{{ $user->name }}</div>
        </div>
        <div style="padding:12px 14px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px;">
            <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">Email</div>
            <div style="font-size:13.5px; color:var(--ink); font-weight:600; margin-top:4px;">{{ $user->email }}</div>
        </div>
        <div style="padding:12px 14px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px;">
            <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">Nomor WhatsApp</div>
            <div style="font-size:13.5px; color:var(--ink); font-weight:600; margin-top:4px;">{{ $mahasiswa->no_hp }}</div>
        </div>
        <div style="padding:12px 14px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px;">
            <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">NIM</div>
            <div style="font-size:13.5px; color:var(--ink); font-weight:600; margin-top:4px;">{{ $mahasiswa->nim }}</div>
        </div>
        <div style="padding:12px 14px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px;">
            <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">Universitas</div>
            <div style="font-size:13.5px; color:var(--ink); font-weight:600; margin-top:4px;">{{ $mahasiswa->universitas }}</div>
        </div>
        <div style="padding:12px 14px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px;">
            <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">Fakultas</div>
            <div style="font-size:13.5px; color:var(--ink); font-weight:600; margin-top:4px;">{{ $mahasiswa->fakultas }}</div>
        </div>
        <div style="padding:12px 14px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px;">
            <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">Program Studi / Jurusan</div>
            <div style="font-size:13.5px; color:var(--ink); font-weight:600; margin-top:4px;">{{ $mahasiswa->jurusan }}</div>
        </div>
        <div style="padding:12px 14px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px;">
            <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">Semester</div>
            <div style="font-size:13.5px; color:var(--ink); font-weight:600; margin-top:4px;">{{ $mahasiswa->semester }}</div>
        </div>
    </div>

    <div style="border-top:1px solid var(--line); padding-top:16px;">
        <div style="font-size:12px; color:var(--muted); font-weight:700; text-transform:uppercase; margin-bottom:10px;">Status Berkas Tersimpan</div>
        <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px;">
            @foreach([
                'proposal_magang' => 'Proposal Magang',
                'surat_pengantar' => 'Surat Pengantar Kampus',
                'cv' => 'Curriculum Vitae (CV)',
                'transkrip' => 'Transkrip Nilai'
            ] as $fileField => $fileLabel)
                <div style="padding:10px 12px; border:1px solid {{ $mahasiswa->{$fileField} ? '#BBF7D0' : '#FDE68A' }}; background:{{ $mahasiswa->{$fileField} ? '#F0FDF4' : '#FFFBEB' }}; border-radius:8px;">
                    <div style="font-size:12px; font-weight:700; color:var(--ink);">{{ $fileLabel }}</div>
                    <div style="font-size:11.5px; color:{{ $mahasiswa->{$fileField} ? '#166534' : '#92400E' }}; margin-top:4px;">
                        {{ $mahasiswa->{$fileField} ? 'Sudah diunggah' : 'Belum diunggah' }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Formulir Berkas Persyaratan --}}
<div class="card">
    <div class="card-header">
        <div>
            <h3>Dokumen Berkas Persyaratan Magang</h3>
            <div class="card-sub">Format file harus berupa dokumen PDF dengan ukuran maksimal 5 MB per file.</div>
        </div>
    </div>

    <form action="{{ route('mahasiswa.pendaftaran.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:24px;">
            <div>
                <label class="form-label">Rencana Tanggal Mulai Magang <span style="font-weight:400; color:var(--muted);">(Opsional)</span></label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', isset($mahasiswa->tanggal_mulai) ? \Carbon\Carbon::parse($mahasiswa->tanggal_mulai)->format('Y-m-d') : '') }}">
                @error('tanggal_mulai')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">Rencana Tanggal Selesai Magang <span style="font-weight:400; color:var(--muted);">(Opsional)</span></label>
                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', isset($mahasiswa->tanggal_selesai) ? \Carbon\Carbon::parse($mahasiswa->tanggal_selesai)->format('Y-m-d') : '') }}">
                @error('tanggal_selesai')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            {{-- 1. Proposal Magang --}}
            <div class="file-dropzone" style="text-align:left; padding:14px 16px;">
                <input type="file" name="proposal_magang" accept=".pdf" {{ ($mahasiswa && $mahasiswa->proposal_magang) ? '' : 'required' }} oninvalid="this.setCustomValidity('Silakan pilih file PDF Proposal Magang terlebih dahulu.')" oninput="this.setCustomValidity('')">
                <div style="display:flex; align-items:flex-start; gap:12px;">
                    <div class="dz-icon" style="margin-bottom:0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div style="flex:1;">
                        <div class="dz-title">Proposal Magang <span style="color:var(--danger);">* Wajib</span></div>
                        <div class="dz-sub">Klik atau seret file PDF proposal ke sini</div>
                        @if($mahasiswa && $mahasiswa->proposal_magang)
                            <div class="dz-filename">
                                File tersimpan : <a href="{{ asset('storage/' . $mahasiswa->proposal_magang) }}" target="_blank" onclick="event.stopPropagation();" style="text-decoration:underline;">Lihat Dokumen</a>
                            </div>
                        @endif
                        @error('proposal_magang')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- 2. Surat Pengantar Kampus --}}
            <div class="file-dropzone" style="text-align:left; padding:14px 16px;">
                <input type="file" name="surat_pengantar" accept=".pdf" {{ ($mahasiswa && $mahasiswa->surat_pengantar) ? '' : 'required' }} oninvalid="this.setCustomValidity('Silakan pilih file PDF Surat Pengantar Kampus terlebih dahulu.')" oninput="this.setCustomValidity('')">
                <div style="display:flex; align-items:flex-start; gap:12px;">
                    <div class="dz-icon" style="margin-bottom:0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div style="flex:1;">
                        <div class="dz-title">Surat Pengantar Kampus <span style="color:var(--danger);">* Wajib</span></div>
                        <div class="dz-sub">Surat permohonan magang dari fakultas / prodi</div>
                        @if($mahasiswa && $mahasiswa->surat_pengantar)
                            <div class="dz-filename">
                                File tersimpan : <a href="{{ asset('storage/' . $mahasiswa->surat_pengantar) }}" target="_blank" onclick="event.stopPropagation();" style="text-decoration:underline;">Lihat Dokumen</a>
                            </div>
                        @endif
                        @error('surat_pengantar')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- 3. Curriculum Vitae --}}
            <div class="file-dropzone" style="text-align:left; padding:14px 16px;">
                <input type="file" name="cv" accept=".pdf">
                <div style="display:flex; align-items:flex-start; gap:12px;">
                    <div class="dz-icon" style="margin-bottom:0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div style="flex:1;">
                        <div class="dz-title">Curriculum Vitae (CV) <span style="font-weight:400; color:var(--muted);">(Opsional)</span></div>
                        <div class="dz-sub">Resume profil dan pengalaman mahasiswa</div>
                        @if($mahasiswa && $mahasiswa->cv)
                            <div class="dz-filename">
                                File tersimpan: <a href="{{ asset('storage/' . $mahasiswa->cv) }}" target="_blank" onclick="event.stopPropagation();" style="text-decoration:underline;">Lihat Dokumen</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 4. Transkrip Nilai --}}
            <div class="file-dropzone" style="text-align:left; padding:14px 16px;">
                <input type="file" name="transkrip" accept=".pdf">
                <div style="display:flex; align-items:flex-start; gap:12px;">
                    <div class="dz-icon" style="margin-bottom:0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div style="flex:1;">
                        <div class="dz-title">Transkrip Nilai Sementara <span style="font-weight:400; color:var(--muted);">(Opsional)</span></div>
                        <div class="dz-sub">KHS atau transkrip nilai semester terakhir</div>
                        @if($mahasiswa && $mahasiswa->transkrip)
                            <div class="dz-filename">
                                File tersimpan: <a href="{{ asset('storage/' . $mahasiswa->transkrip) }}" target="_blank" onclick="event.stopPropagation();" style="text-decoration:underline;">Lihat Dokumen</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--line); padding-top:20px; margin-top:24px;">
            <a href="{{ route('mahasiswa.profil.lengkapi') }}" class="btn btn-ghost">
                Kembali ke Profil Akademik
            </a>
            <button type="submit" class="btn btn-solid" style="padding:10px 24px; font-weight:700;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Kirim Pengajuan Pendaftaran Magang
            </button>
        </div>
    </form>
</div>
@endsection
