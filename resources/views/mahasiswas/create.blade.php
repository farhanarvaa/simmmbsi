@extends('layouts.app')

@section('title', 'SIMMM - Form Mahasiswa Magang')

@section('content')
<x-topbar :title="isset($mahasiswa) ? 'Edit Mahasiswa Magang' : 'Tambah Mahasiswa Magang'" subtitle="Kelola informasi data diri dan unit kerja penempatan magang." />

<div class="card" style="max-width:850px;">
    <div class="card-header">
        <div>
            <h3>Formulir Data Mahasiswa</h3>
            <div class="card-sub">Lengkapi seluruh kolom identitas dan tanggal pelaksanaan magang.</div>
        </div>
    </div>

    <form action="{{ isset($mahasiswa) ? route('mahasiswas.update', $mahasiswa->id) : route('mahasiswas.store') }}" method="POST" id="form-mahasiswa">
        @csrf
        @if(isset($mahasiswa))
            @method('PUT')
        @endif

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">NIM <span style="color:var(--danger);">*</span></label>
                <input type="text" name="nim" value="{{ old('nim', $mahasiswa->nim ?? '') }}" placeholder="Contoh: 2111522001" required style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif;">
                @error('nim')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Nama Lengkap <span style="color:var(--danger);">*</span></label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $mahasiswa->nama_lengkap ?? '') }}" placeholder="Nama lengkap mahasiswa" required style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif;">
                @error('nama_lengkap')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Universitas <span style="color:var(--danger);">*</span></label>
                <input type="text" name="universitas" value="{{ old('universitas', $mahasiswa->universitas ?? '') }}" placeholder="Nama perguruan tinggi" required style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif;">
                @error('universitas')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Jurusan / Program Studi <span style="color:var(--danger);">*</span></label>
                <input type="text" name="jurusan" value="{{ old('jurusan', $mahasiswa->jurusan ?? '') }}" placeholder="Contoh: Sistem Informasi" required style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif;">
                @error('jurusan')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Cabang/Unit Penempatan <span style="color:var(--danger);">*</span></label>
                <select name="id_cabang" required style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif;">
                    <option value="">-- Pilih Cabang/Unit --</option>
                    @php $selectedCabang = old('id_cabang', $mahasiswa->id_cabang ?? ''); @endphp
                    @foreach($cabangs as $cabang)
                        <option value="{{ $cabang->id }}" {{ (string) $selectedCabang === (string) $cabang->id ? 'selected' : '' }}>{{ $cabang->nama }}</option>
                    @endforeach
                </select>
                @error('id_cabang')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Status Peserta <span style="color:var(--danger);">*</span></label>
                <select name="status" required style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif;">
                    @php $selectedStat = old('status', $mahasiswa->status ?? 'aktif'); @endphp
                    <option value="aktif" {{ $selectedStat == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="selesai" {{ $selectedStat == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ $selectedStat == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Tanggal Mulai <span style="color:var(--danger);">*</span></label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', isset($mahasiswa) && $mahasiswa->tanggal_mulai ? \Carbon\Carbon::parse($mahasiswa->tanggal_mulai)->format('Y-m-d') : '') }}" required style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif;">
                @error('tanggal_mulai')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Tanggal Selesai <span style="font-weight:400; color:var(--muted);">(Opsional)</span></label>
                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', isset($mahasiswa) && $mahasiswa->tanggal_selesai ? \Carbon\Carbon::parse($mahasiswa->tanggal_selesai)->format('Y-m-d') : '') }}" style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif;">
                @error('tanggal_selesai')<div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>

        <div style="display:flex; gap:10px; margin-top:20px;">
            <a href="{{ route('mahasiswas.index') }}" class="btn btn-ghost">Kembali</a>
            <button type="submit" class="btn btn-solid" id="btn-submit-mhs">
                <span id="btn-label-mhs">{{ isset($mahasiswa) ? 'Perbarui Data' : 'Simpan Data' }}</span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-mahasiswa');
    const submitBtn = document.getElementById('btn-submit-mhs');
    const btnLabel = document.getElementById('btn-label-mhs');

    if (form && submitBtn) {
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
            submitBtn.style.cursor = 'not-allowed';
            if (btnLabel) btnLabel.textContent = 'Menyimpan...';
        });
    }
});
</script>
@endsection
