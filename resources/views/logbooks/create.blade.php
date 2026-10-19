@extends('layouts.app')

@section('title', 'SIMMM - Form Logbook')

@section('content')
<x-topbar :title="isset($logbook) ? 'Edit Logbook' : 'Input Logbook Kegiatan'" subtitle="Catat detail pekerjaan dan pencapaian harianmu." />

<div class="card" style="max-width:780px;">
    <div class="card-header">
        <div>
            <h3>Formulir Aktivitas</h3>
            <div class="card-sub">Laporan ini akan ditinjau oleh Admin/Mentor.</div>
        </div>
    </div>

    @if ($errors->any())
        <div style="background:var(--danger-bg); color:var(--danger); border:1px solid #efc0b9; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13.5px;">
            <div style="font-weight:700; margin-bottom:6px; display:flex; align-items:center; gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Terdapat kesalahan pada formulir:
            </div>
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(isset($logbook) && $logbook->status === 'revisi')
        <div style="background:var(--amber-bg); color:var(--amber); border:1px solid #e5cc9c; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13.5px; display:flex; gap:10px; align-items:flex-start;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:1px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <div><strong>Catatan Revisi :</strong> Logbook ini diminta revisi oleh Admin/Mentor. Setelah diperbaiki, simpan untuk mengirim ulang.</div>
        </div>
    @endif

    <form method="POST" action="{{ isset($logbook) ? route('logbooks.update', $logbook) : route('logbooks.store') }}" id="logbook-form">
        @csrf
        @if(isset($logbook))
            @method('PUT')
        @endif

        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">
                Tanggal <span style="color:var(--danger);">*</span>
            </label>
            <input type="date" name="tanggal"
                value="{{ old('tanggal', isset($logbook) ? \Illuminate\Support\Carbon::parse($logbook->tanggal)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                required
                style="width:100%; border:1px solid var(--line); border-radius:8px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif; max-width:260px;">
        </div>

        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">
                Judul Kegiatan <span style="color:var(--danger);">*</span>
            </label>
            <input type="text" name="judul" value="{{ old('judul', $logbook->judul ?? '') }}"
                placeholder="Contoh: Rekapitulasi Berkas Pendaftar Magang"
                maxlength="150" required
                style="width:100%; border:1px solid var(--line); border-radius:8px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif;">
            <div style="font-size:11.5px; color:var(--muted); margin-top:3px;">Maksimal 150 karakter.</div>
        </div>

        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">
                Aktivitas Detail <span style="color:var(--danger);">*</span>
            </label>
            <textarea name="aktivitas" placeholder="Jelaskan langkah-langkah kegiatan yang dikerjakan hari ini"
                required
                style="width:100%; border:1px solid var(--line); border-radius:8px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif; min-height:100px; resize:vertical;">{{ old('aktivitas', $logbook->aktivitas ?? '') }}</textarea>
        </div>

        <div style="margin-bottom:24px;">
            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">
                Hasil / Output
                <span style="font-weight:400; color:var(--muted);">(Opsional)</span>
            </label>
            <textarea name="hasil" placeholder="Hasil atau output yang dicapai dari kegiatan tersebut"
                style="width:100%; border:1px solid var(--line); border-radius:8px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif; min-height:80px; resize:vertical;">{{ old('hasil', $logbook->hasil ?? '') }}</textarea>
        </div>

        <input type="hidden" name="status" value="diajukan">

        <div style="display:flex; gap:10px; align-items:center; border-top:1px solid var(--line); padding-top:18px;">
            <a href="{{ route('logbooks.index') }}" class="btn btn-ghost">Kembali</a>
            <button type="submit" class="btn btn-solid" id="logbook-submit-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <span id="logbook-btn-label">{{ isset($logbook) ? 'Perbarui Logbook' : 'Simpan Logbook' }}</span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('logbook-form');
    const submitBtn = document.getElementById('logbook-submit-btn');
    const btnLabel = document.getElementById('logbook-btn-label');

    if (form && submitBtn) {
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
            submitBtn.style.cursor = 'not-allowed';
            btnLabel.textContent = 'Menyimpan...';
        });
    }
});
</script>
@endsection
