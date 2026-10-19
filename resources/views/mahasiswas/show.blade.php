@extends('layouts.app')

@section('title', 'SIMMM - Detail Mahasiswa Magang')

@section('content')
<div class="topbar">
    <div>
        <h1>Detail Mahasiswa Magang</h1>
        <div class="subtitle">Informasi lengkap data mahasiswa (hanya baca).</div>
    </div>
</div>

<div class="card" style="max-width:850px;">
    <div class="card-header">
        <div>
            <h3>{{ $mahasiswa->nama_lengkap }}</h3>
            <div class="card-sub">NIM: {{ $mahasiswa->nim }}</div>
        </div>
        <a href="{{ route('mahasiswas.index') }}" class="btn btn-ghost">← Kembali</a>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; padding:20px 0;">
        <div>
            <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:4px;">UNIVERSITAS</div>
            <div style="font-size:14px;">{{ $mahasiswa->universitas ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:4px;">JURUSAN / PRODI</div>
            <div style="font-size:14px;">{{ $mahasiswa->jurusan ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:4px;">DIVISI BSI</div>
            <div style="font-size:14px;">{{ $mahasiswa->cabang?->nama ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:4px;">NAMA MENTOR</div>
            <div style="font-size:14px;">{{ $mahasiswa->nama_mentor ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:4px;">TANGGAL MULAI</div>
            <div style="font-size:14px;">{{ $mahasiswa->tanggal_mulai ? \Carbon\Carbon::parse($mahasiswa->tanggal_mulai)->isoFormat('D MMMM Y') : '-' }}</div>
        </div>
        <div>
            <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:4px;">TANGGAL SELESAI</div>
            <div style="font-size:14px;">{{ $mahasiswa->tanggal_selesai ? \Carbon\Carbon::parse($mahasiswa->tanggal_selesai)->isoFormat('D MMMM Y') : '-' }}</div>
        </div>
        <div>
            <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:4px;">STATUS PESERTA</div>
            <div style="font-size:14px;">
                @php
                    $statusColor = ['aktif' => '#22c55e', 'selesai' => '#3b82f6', 'dibatalkan' => '#ef4444'];
                @endphp
                <span style="color:{{ $statusColor[$mahasiswa->status] ?? 'inherit' }}; font-weight:600;">
                    {{ ucfirst($mahasiswa->status ?? '-') }}
                </span>
            </div>
        </div>
        <div>
            <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:4px;">STATUS PENDAFTARAN</div>
            <div style="font-size:14px; font-weight:600;">{{ ucfirst($mahasiswa->status_pendaftaran ?? '-') }}</div>
        </div>
        @if($mahasiswa->catatan_revisi)
        <div style="grid-column:1/-1;">
            <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:4px;">CATATAN REVISI</div>
            <div style="font-size:14px; color:#ef4444;">{{ $mahasiswa->catatan_revisi }}</div>
        </div>
        @endif
    </div>

    <div style="display:flex; gap:10px; padding-top:16px; border-top:1px solid var(--line);">
        <a href="{{ route('mahasiswas.index') }}" class="btn btn-ghost">Kembali</a>
        <a href="{{ route('mahasiswas.edit', $mahasiswa->id) }}" class="btn btn-solid">Edit Data</a>
    </div>
</div>
@endsection
