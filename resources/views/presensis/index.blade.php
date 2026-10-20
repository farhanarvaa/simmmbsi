@extends('layouts.app')

@section('title', 'SIMMM - Data Presensi')

@section('content')
@php 
    $user = auth()->user();
    $isAdmin = $user->role === 'admin'; 
    $isSelesai = !$isAdmin && ($user->mahasiswa?->status === 'selesai');
    // State presensi hari ini
    $hariIniSudahDatang  = !$isAdmin && $presensiHariIni && $presensiHariIni->jam_masuk;
    $hariIniSudahPulang  = !$isAdmin && $presensiHariIni && $presensiHariIni->jam_masuk && $presensiHariIni->jam_pulang;
    $hariIniIzinSakit    = !$isAdmin && $presensiHariIni && !$presensiHariIni->jam_masuk;
@endphp

<x-topbar :title="$isAdmin ? 'Rekap Presensi Mahasiswa' : 'Data Presensi Magang'" :subtitle="$isAdmin ? 'Rekapitulasi kehadiran seluruh mahasiswa magang beserta bukti foto yang memiliki watermark waktu & lokasi.' : ($isSelesai ? 'Riwayat kehadiran harian selama pelaksanaan magang.' : 'Rekapitulasi kehadiran mahasiswa magang Bank Syariah Indonesia Area Yogyakarta.')">
    <x-slot:actions>
        @if($isSelesai)
            <span class="badge badge-ok" style="background:#e7f3ed; color:var(--green); font-weight:700;">Magang Selesai</span>
        @elseif(!$isAdmin)
            @if($hariIniSudahPulang || $hariIniIzinSakit)
                {{-- Presensi hari ini selesai --}}
                <span class="badge badge-ok" style="background:#e7f3ed; color:var(--green); font-weight:700;">Presensi Hari Ini : Selesai</span>
            @elseif($hariIniSudahDatang)
                {{-- Sudah masuk, belum pulang --}}
                <button type="button" class="btn btn-solid" onclick="openPresensiModal()" style="background:var(--amber, #f59e0b);">
                    <span>Presensi Pulang</span>
                </button>
            @else
                {{-- Belum melakukan presensi sama sekali --}}
                <button type="button" class="btn btn-solid" onclick="openPresensiModal()">  
                    <span>Presensi Masuk</span>
                </button>
            @endif
        @endif
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
</div>

<div class="card" id="presensi-card">
    <div class="card-header">
        <div>
            <h3>Daftar Presensi Harian</h3>
            <div class="card-sub">{{ $isAdmin ? 'Data kehadiran semua mahasiswa yang terekam di sistem.' : ($isSelesai ? 'Riwayat lengkap kehadiran selama masa program magang.' : 'Riwayat log masuk, pulang, serta bukti foto kehadiran.') }}</div>
        </div>
        @if($isSelesai)
            <div style="font-size:12px; color:var(--muted); font-style:italic;">Mode arsip, tidak dapat menambah/mengubah data</div>
        @endif
    </div>

    <div id="presensi-table-container">
        @if($presensis->isEmpty())
            <div class="empty-state" id="presensi-empty-state">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3>Belum Ada Data Presensi</h3>
                <p>{{ $isAdmin ? 'Belum ada mahasiswa yang mengisi presensi.' : ($isSelesai ? 'Tidak ada catatan presensi dalam riwayat magang.' : 'Belum ada catatan presensi. Klik tombol di atas untuk mengisi presensi harian.') }}</p>
            </div>
        @else
            <div class="table-responsive">
                <table id="presensi-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Lokasi</th>
                            <th>Bukti Foto</th>
                            <th>Mahasiswa</th>
                            @if(!$isAdmin && !$isSelesai)<th>Aksi</th>@endif
                        </tr>
                    </thead>
                    <tbody id="presensi-tbody">
                        @foreach($presensis as $presensi)
                            <tr data-id="{{ $presensi->id }}" data-tanggal="{{ \Illuminate\Support\Carbon::parse($presensi->tanggal)->format('Y-m-d') }}">
                                <td>{{ \Illuminate\Support\Carbon::parse($presensi->tanggal)->format('d-m-Y') }}</td>
                                <td>
                                    @if(strtolower($presensi->status) === 'hadir')
                                        <span class="badge badge-ok">HADIR</span>
                                    @elseif(strtolower($presensi->status) === 'izin' || strtolower($presensi->status) === 'sakit')
                                        <span class="badge badge-wait">{{ strtoupper($presensi->status) }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ strtoupper($presensi->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $presensi->jam_masuk ? \Illuminate\Support\Carbon::parse($presensi->jam_masuk)->format('H:i') : '-' }}</td>
                                <td>{{ $presensi->jam_pulang ? \Illuminate\Support\Carbon::parse($presensi->jam_pulang)->format('H:i') : '-' }}</td>
                                <td>{{ $presensi->lokasi ?: '-' }}</td>
                                <td>
                                    @if($presensi->bukti_path)
                                        <button type="button" class="btn btn-ghost btn-sm" style="padding:4px 10px; font-size:12px;" onclick="viewPhotoModal('{{ asset('storage/' . $presensi->bukti_path) }}', '{{ \Illuminate\Support\Carbon::parse($presensi->tanggal)->format('d-m-Y') }}', '{{ $presensi->user->name ?? '-' }}', '{{ strtoupper($presensi->status) }}', '{{ $presensi->lokasi ?: '-' }}', '{{ $presensi->jam_masuk ? \Illuminate\Support\Carbon::parse($presensi->jam_masuk)->format('H:i') : '-' }}', '{{ $presensi->catatan ?: '-' }}')">
                                            <span>Lihat Foto</span>
                                        </button>
                                    @else
                                        <span style="color:var(--muted); font-size:12px;">-</span>
                                    @endif
                                </td>
                                <td><strong>{{ $presensi->user->name ?? '-' }}</strong></td>
                                @if(!$isAdmin && !$isSelesai)
                                <td>
                                    <div style="display:flex; gap:6px;">
                                        <a class="btn btn-ghost btn-sm" href="{{ route('presensis.edit', $presensi) }}">Edit</a>
                                        <form action="{{ route('presensis.destroy', $presensi) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus catatan presensi ini?" data-title="Hapus Presensi">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($presensis->hasPages())
                <div class="pagination-container">
                    {{ $presensis->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

@if(!$isAdmin && !$isSelesai && !$hariIniSudahPulang && !$hariIniIzinSakit)
{{-- Modal Tambah Presensi Harian --}}
{{-- State dari server: sudah datang hari ini? --}}
<div id="presensi-modal" class="modal-overlay" aria-hidden="true"
     data-sudah-datang="{{ $hariIniSudahDatang ? '1' : '0' }}"
     data-presensi-id="{{ $presensiHariIni?->id ?? '' }}"
     data-jam-masuk="{{ $hariIniSudahDatang ? \Illuminate\Support\Carbon::parse($presensiHariIni->jam_masuk)->format('H:i') : '' }}">
    <div class="modal-box" style="max-width:580px; max-height:92vh; overflow-y:auto;" role="dialog" aria-modal="true" aria-labelledby="presensi-modal-title">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--line); padding-bottom:12px;">
            <div>
                <div id="presensi-modal-title" class="modal-title" style="margin:0; font-size:18px; font-weight:700; color:var(--primary-dark);">Input Presensi Harian</div>
                <div style="font-size:12.5px; color:var(--muted); margin-top:2px;">Isi data dan lampirkan bukti foto yang memiliki watermark.</div>
            </div>
            <button type="button" onclick="closePresensiModal()" aria-label="Tutup modal" style="background:none; border:none; cursor:pointer; color:var(--muted); font-size:24px; line-height:1; padding:0 4px;">&times;</button>
        </div>

        {{-- Notes / Ketentuan Bukti Foto --}}
        <div style="background:linear-gradient(135deg, #e7f3ed 0%, #d4e5e0 100%); border:1px solid #bce3d0; border-radius:8px; padding:12px 14px; margin-bottom:16px; display:flex; gap:10px; align-items:flex-start;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--primary); flex-shrink:0; margin-top:2px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <div style="font-size:12px; line-height:1.45; color:var(--ink-soft);">
                <div style="font-weight:700; color:var(--primary-dark); margin-bottom:2px;">Ketentuan Foto Presensi (Wajib Waktu &amp; Lokasi):</div>
                Foto selfie di kantor yang memiliki <strong>watermark Waktu (Tanggal/Jam)</strong> dan <strong>Lokasi (Geotag)</strong> (contoh: menggunakan aplikasi <em>Timestamp Camera</em> / <em>GPS Map Camera</em>).
            </div>
        </div>

        <div id="presensi-error-box" style="display:none; background:var(--danger-bg); color:var(--danger); border:1px solid #efc0b9; padding:10px 14px; border-radius:6px; margin-bottom:16px; font-size:13px; line-height:1.4;">
            <div id="presensi-error-message"></div>
        </div>

        <form id="presensi-form" action="{{ route('presensis.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="modal-field-status-wrap" style="margin-bottom:14px;">
                <div style="margin-bottom:10px;">
                    <label style="display:block; font-size:12.5px; font-weight:600; margin-bottom:5px;">Tanggal Presensi <span style="color:var(--danger);">*</span></label>
                    <input type="date" name="tanggal" id="presensi-tanggal" value="{{ now()->format('Y-m-d') }}" required style="width:100%; border:1px solid var(--line); border-radius:6px; padding:9px 12px; font-size:13.5px; font-family:'Inter',sans-serif;">
                </div>
                <div>
                    <label style="display:block; font-size:12.5px; font-weight:600; margin-bottom:6px;">Status Kehadiran <span style="color:var(--danger);">*</span></label>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;" role="radiogroup" aria-label="Status Kehadiran">
                        <label id="modal-label-status-hadir" style="flex:1; min-width:80px; display:flex; align-items:center; gap:7px; border:2px solid var(--primary); border-radius:7px; padding:8px 12px; cursor:pointer; background:var(--primary-bg, #e7f3ed); transition:all .2s;">
                            <input type="radio" name="status" id="modal-status-hadir" value="hadir" checked style="accent-color:var(--primary); width:14px; height:14px; flex-shrink:0;">
                            <span style="font-size:12.5px; font-weight:600; color:var(--primary);">Hadir</span>
                        </label>
                        <label id="modal-label-status-sakit" style="flex:1; min-width:80px; display:flex; align-items:center; gap:7px; border:2px solid var(--line); border-radius:7px; padding:8px 12px; cursor:pointer; background:#fff; transition:all .2s;">
                            <input type="radio" name="status" id="modal-status-sakit" value="sakit" style="accent-color:var(--primary); width:14px; height:14px; flex-shrink:0;">
                            <span style="font-size:12.5px; font-weight:600; color:var(--ink-soft);">Sakit</span>
                        </label>
                        <label id="modal-label-status-izin" style="flex:1; min-width:80px; display:flex; align-items:center; gap:7px; border:2px solid var(--line); border-radius:7px; padding:8px 12px; cursor:pointer; background:#fff; transition:all .2s;">
                            <input type="radio" name="status" id="modal-status-izin" value="izin" style="accent-color:var(--primary); width:14px; height:14px; flex-shrink:0;">
                            <span style="font-size:12.5px; font-weight:600; color:var(--ink-soft);">Izin</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Jenis Presensi: Datang / Pulang --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:12.5px; font-weight:600; margin-bottom:8px;">Jenis Presensi <span style="color:var(--danger);">*</span></label>
                <div style="display:flex; gap:10px;">
                    <label id="modal-label-datang" style="flex:1; display:flex; align-items:center; gap:8px; border:2px solid var(--primary); border-radius:8px; padding:10px 14px; cursor:pointer; background:var(--primary-bg, #e7f3ed); transition:all .2s;">
                        <input type="radio" name="jenis_presensi" id="modal-jenis-datang" value="datang" checked style="accent-color:var(--primary); width:15px; height:15px;">
                        <span style="font-size:13px; font-weight:600; color:var(--primary);">Presensi Masuk</span>
                    </label>
                    <label id="modal-label-pulang" style="flex:1; display:flex; align-items:center; gap:8px; border:2px solid var(--line); border-radius:8px; padding:10px 14px; cursor:pointer; background:#fff; transition:all .2s;">
                        <input type="radio" name="jenis_presensi" id="modal-jenis-pulang" value="pulang" style="accent-color:var(--primary); width:15px; height:15px;">
                        <span style="font-size:13px; font-weight:600; color:var(--ink-soft);">Presensi Pulang</span>
                    </label>
                </div>
                {{-- Info jam masuk (hanya tampil saat mode pulang) --}}
                <div id="modal-info-jam-masuk" style="display:none; margin-top:8px; background:#e7f3ed; border:1px solid #bce3d0; border-radius:6px; padding:8px 12px; font-size:12px; color:var(--primary-dark); font-weight:600;"></div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                <div id="modal-field-jam-masuk">
                    <label style="display:block; font-size:12.5px; font-weight:600; margin-bottom:5px;">Jam Masuk <span style="color:var(--danger);" class="modal-req-star">*</span></label>
                    <div style="position:relative;">
                        <input type="time" name="jam_masuk" id="presensi-jam-masuk" readonly
                            style="width:100%; border:1px solid var(--line); border-radius:6px; padding:9px 12px; font-size:13.5px; font-family:'Inter',sans-serif; background:#f5f5f5; cursor:not-allowed;">
                        <span style="position:absolute; right:8px; top:50%; transform:translateY(-50%); font-size:10px; color:var(--muted); pointer-events:none;">Otomatis</span>
                    </div>
                </div>
                <div id="modal-field-jam-pulang" style="display:none;">
                    <label style="display:block; font-size:12.5px; font-weight:600; margin-bottom:5px;">Jam Pulang <span style="font-weight:400; color:var(--muted);">(Opsional)</span></label>
                    <div style="position:relative;">
                        <input type="time" name="jam_pulang" id="presensi-jam-pulang" readonly
                            style="width:100%; border:1px solid var(--line); border-radius:6px; padding:9px 12px; font-size:13.5px; font-family:'Inter',sans-serif; background:#f5f5f5; cursor:not-allowed;">
                        <span style="position:absolute; right:8px; top:50%; transform:translateY(-50%); font-size:10px; color:var(--muted); pointer-events:none;">Otomatis</span>
                    </div>
                </div>
            </div>

            <div style="margin-bottom:14px;" id="modal-field-lokasi">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
                    <label style="font-size:12.5px; font-weight:600; margin:0;">Lokasi Kegiatan <span style="color:var(--danger);" class="modal-req-star">*</span></label>
                    <button type="button" id="modal-btn-detect" class="btn btn-ghost btn-sm" style="padding:2px 8px; font-size:11px; height:auto; display:inline-flex; gap:4px; align-items:center;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>Deteksi Lokasi &amp; Waktu</span>
                    </button>
                </div>
                <input type="text" name="lokasi" id="presensi-lokasi" placeholder="Contoh: BSI Area Yogyakarta" required style="width:100%; border:1px solid var(--line); border-radius:6px; padding:9px 12px; font-size:13.5px; font-family:'Inter',sans-serif;">
            </div>

            {{-- Upload File Bukti Presensi --}}
            <div style="margin-bottom:14px;" id="modal-field-bukti">
                <label style="display:block; font-size:12.5px; font-weight:600; margin-bottom:5px;">
                    Upload Foto Bukti Presensi <span style="color:var(--danger);" id="modal-bukti-req-star">*</span>
                    <span style="font-size:11.5px; font-weight:400; color:var(--muted);">(Format : JPG, PNG, WEBP, Maks. 5MB)</span>
                </label>

                <div class="file-dropzone" id="modal-file-dropzone" style="padding:16px 14px; border-radius:8px;">
                    <input type="file" name="bukti" id="presensi-bukti" accept="image/*" required>
                    <div class="dz-icon" style="margin-bottom:4px;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                    <div class="dz-title" style="font-size:12.5px;">Pilih / Tarik Foto Bukti Kehadiran di Sini</div>
                    <div class="dz-sub" style="font-size:11px;">Wajib memuat watermark tanggal, jam, dan lokasi</div>
                    <div id="modal-dz-filename" class="dz-filename" style="display:none; margin-top:6px; font-size:11.5px;"></div>
                </div>

                {{-- Preview Area --}}
                <div id="modal-preview-box" style="display:none; margin-top:10px; padding:10px; background:var(--bg); border:1px solid var(--line); border-radius:6px;">
                    <div style="display:flex; gap:12px; align-items:center;">
                        <img id="modal-preview-img" src="#" alt="Pratinjau" style="width:55px; height:55px; border-radius:6px; object-fit:cover; border:1px solid var(--line);">
                        <div style="font-size:11.5px; color:var(--ink-soft); line-height:1.4;">
                            <div id="modal-preview-name" style="font-weight:600; word-break:break-all;">-</div>
                            <div style="color:var(--green); font-weight:500;">✓ Foto siap diunggah</div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-bottom:18px;" id="modal-field-catatan">
                <label style="display:block; font-size:12.5px; font-weight:600; margin-bottom:5px;">
                    Catatan / Alasan <span id="modal-catatan-req-indicator" style="font-weight:400; color:var(--muted);">(Opsional)</span>
                </label>
                <textarea name="catatan" id="presensi-catatan" placeholder="Catatan aktivitas singkat atau alasan izin/sakit" style="width:100%; border:1px solid var(--line); border-radius:6px; padding:9px 12px; font-size:13.5px; font-family:'Inter',sans-serif; min-height:60px; resize:vertical;"></textarea>
            </div>

            <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--line); padding-top:14px;">
                <button type="button" class="btn btn-ghost" onclick="closePresensiModal()">Batal</button>
                <button type="submit" id="presensi-submit-btn" class="btn btn-solid">
                    <span id="presensi-btn-label">Simpan Presensi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Modal Preview Foto Bukti Presensi (Untuk Mahasiswa & Admin) --}}
<div id="photo-preview-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:540px; padding:22px;" role="dialog" aria-modal="true">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid var(--line); padding-bottom:10px;">
            <div>
                <div class="modal-title" style="margin:0; font-size:17px; font-weight:700; color:var(--primary-dark);">Bukti Foto Presensi</div>
                <div id="photo-modal-subtitle" style="font-size:12px; color:var(--muted); margin-top:2px;">Detail kehadiran &amp; dokumentasi magang</div>
            </div>
            <button type="button" onclick="closePhotoModal()" aria-label="Tutup" style="background:none; border:none; cursor:pointer; color:var(--muted); font-size:24px; line-height:1; padding:0 4px;">&times;</button>
        </div>

        <div style="text-align:center; background:#0e1715; border-radius:8px; padding:8px; margin-bottom:14px; overflow:hidden;">
            <img id="photo-modal-img" src="" alt="Bukti Foto Presensi" style="max-width:100%; max-height:380px; object-fit:contain; border-radius:4px;">
        </div>

        <div style="background:var(--bg); border:1px solid var(--line); border-radius:8px; padding:12px 14px; font-size:12.5px; line-height:1.6;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                <div><span style="color:var(--muted);">Mahasiswa:</span> <strong id="photo-modal-student">-</strong></div>
                <div><span style="color:var(--muted);">Tanggal:</span> <strong id="photo-modal-date">-</strong></div>
                <div><span style="color:var(--muted);">Status:</span> <span id="photo-modal-status" class="badge badge-ok" style="font-size:10px; padding:2px 8px;">HADIR</span></div>
                <div><span style="color:var(--muted);">Jam Masuk:</span> <strong id="photo-modal-time">-</strong></div>
            </div>
            <div style="margin-top:8px; border-top:1px dashed var(--line); padding-top:6px;">
                <div><span style="color:var(--muted);">Lokasi:</span> <span id="photo-modal-location" style="font-weight:600;">-</span></div>
                <div style="margin-top:3px;"><span style="color:var(--muted);">Catatan:</span> <span id="photo-modal-note" style="font-style:italic;">-</span></div>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px;">
            <a id="photo-modal-link" href="#" target="_blank" class="btn btn-ghost btn-sm" style="font-size:12px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                <span>Buka Ukuran Asli</span>
            </a>
            <button type="button" class="btn btn-solid btn-sm" onclick="closePhotoModal()">Tutup</button>
        </div>
    </div>
</div>

<script>
    const presensiModal = document.getElementById('presensi-modal');
    const presensiForm = document.getElementById('presensi-form');
    const presensiErrorBox = document.getElementById('presensi-error-box');
    const presensiErrorMessage = document.getElementById('presensi-error-message');
    const presensiSubmitBtn = document.getElementById('presensi-submit-btn');
    const presensiBtnLabel = document.getElementById('presensi-btn-label');
    const photoModal = document.getElementById('photo-preview-modal');

    function openPresensiModal() {
        if (!presensiModal) return;
        hidePresensiError();
        presensiModal.classList.add('active');
        presensiModal.setAttribute('aria-hidden', 'false');

        // Baca state dari server
        const sudahDatang = presensiModal.dataset.sudahDatang === '1';
        const jamMasukServer = presensiModal.dataset.jamMasuk || '';

        const jenisDatang = document.getElementById('modal-jenis-datang');
        const jenisPulang = document.getElementById('modal-jenis-pulang');
        const labelDatang = document.getElementById('modal-label-datang');
        const labelPulang = document.getElementById('modal-label-pulang');
        const modalTitle  = document.getElementById('presensi-modal-title');

        if (sudahDatang && jenisDatang && jenisPulang) {
            // Sudah melakukan Presensi Masuk -> paksa mode Pulang
            jenisPulang.checked  = true;
            jenisDatang.disabled = true;
            labelDatang.style.opacity = '0.45';
            labelDatang.style.cursor  = 'not-allowed';
            if (modalTitle) modalTitle.textContent = 'Presensi Pulang';
            // Tampilkan info jam masuk sebelumnya
            const infoMasuk = document.getElementById('modal-info-jam-masuk');
            if (infoMasuk) { infoMasuk.textContent = `Jam masuk tercatat: ${jamMasukServer}`; infoMasuk.style.display = 'block'; }
            if (typeof applyModalJenis === 'function') applyModalJenis();

            // Sembunyikan field yang tidak perlu untuk mode pulang
            const fieldStatus = document.getElementById('modal-field-status-wrap');
            if (fieldStatus) fieldStatus.style.display = 'none';
            const fieldLokasi = document.getElementById('modal-field-lokasi');
            if (fieldLokasi) fieldLokasi.style.display = 'none';
            const fieldBukti = document.getElementById('modal-field-bukti');
            if (fieldBukti) fieldBukti.style.display = 'none';
            const fieldCatatan = document.getElementById('modal-field-catatan');
            if (fieldCatatan) fieldCatatan.style.display = 'none';
        } else {
            // Belum melakukan Presensi Masuk -> mode Masuk normal
            if (jenisDatang) { jenisDatang.checked = true; jenisDatang.disabled = false; }
            if (labelDatang) { labelDatang.style.opacity = '1'; labelDatang.style.cursor = 'pointer'; }
            if (modalTitle) modalTitle.textContent = 'Presensi Masuk';
            const infoMasuk = document.getElementById('modal-info-jam-masuk');
            if (infoMasuk) infoMasuk.style.display = 'none';
            const fieldStatus = document.getElementById('modal-field-status-wrap');
            if (fieldStatus) fieldStatus.style.display = '';
            const fieldLokasi = document.getElementById('modal-field-lokasi');
            if (fieldLokasi) fieldLokasi.style.display = '';
            const fieldBukti = document.getElementById('modal-field-bukti');
            if (fieldBukti) fieldBukti.style.display = '';
            const fieldCatatan = document.getElementById('modal-field-catatan');
            if (fieldCatatan) fieldCatatan.style.display = '';
            if (typeof applyModalJenis === 'function') applyModalJenis();
        }

        updateModalRequirements();
    }

    function closePresensiModal() {
        if (!presensiModal) return;
        presensiModal.classList.remove('active');
        presensiModal.setAttribute('aria-hidden', 'true');
        hidePresensiError();
        if (presensiForm) presensiForm.reset();
        const dateInput = document.getElementById('presensi-tanggal');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;
        }
        // Reset jenis presensi ke Datang dan jam otomatis
        const jenisDatang = document.getElementById('modal-jenis-datang');
        if (jenisDatang) {
            jenisDatang.checked = true;
            applyModalJenis();
        }

        const previewBox = document.getElementById('modal-preview-box');
        if (previewBox) previewBox.style.display = 'none';
        const dzFilename = document.getElementById('modal-dz-filename');
        if (dzFilename) dzFilename.style.display = 'none';
    }

    function viewPhotoModal(url, date, student, status, location, time, note) {
        if (!photoModal) return;
        document.getElementById('photo-modal-img').src = url;
        document.getElementById('photo-modal-link').href = url;
        document.getElementById('photo-modal-date').textContent = date;
        document.getElementById('photo-modal-student').textContent = student;
        document.getElementById('photo-modal-location').textContent = location;
        document.getElementById('photo-modal-time').textContent = time;
        document.getElementById('photo-modal-note').textContent = note && note !== '-' ? note : 'Tidak ada catatan';
        
        const statusEl = document.getElementById('photo-modal-status');
        statusEl.textContent = status;
        if (status === 'HADIR') {
            statusEl.className = 'badge badge-ok';
        } else if (status === 'IZIN' || status === 'SAKIT') {
            statusEl.className = 'badge badge-wait';
        } else {
            statusEl.className = 'badge badge-danger';
        }

        photoModal.classList.add('active');
        photoModal.setAttribute('aria-hidden', 'false');
    }

    function closePhotoModal() {
        if (!photoModal) return;
        photoModal.classList.remove('active');
        photoModal.setAttribute('aria-hidden', 'true');
    }

    function showPresensiError(msg) {
        if (!presensiErrorBox || !presensiErrorMessage) return;
        presensiErrorMessage.innerHTML = msg;
        presensiErrorBox.style.display = 'block';
    }

    function hidePresensiError() {
        if (!presensiErrorBox) return;
        presensiErrorBox.style.display = 'none';
        if (presensiErrorMessage) presensiErrorMessage.innerHTML = '';
    }

    function showToastNotification(message) {
        const container = document.getElementById('flash-container');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = 'flash';
        toast.innerHTML = `
            <div style="display:flex; align-items:center; gap:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>${message}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
        `;
        container.prepend(toast);
        setTimeout(() => {
            if (toast && toast.parentElement) toast.remove();
        }, 6000);
    }

    // Helper: dapatkan nilai radio status modal yang sedang terpilih
    function getModalSelectedStatus() {
        const checked = document.querySelector('input[name="status"]:checked');
        return checked ? checked.value : 'hadir';
    }

    // Helper: update style card radio status modal
    function applyModalStatusStyle() {
        const map = {
            'hadir': document.getElementById('modal-label-status-hadir'),
            'sakit': document.getElementById('modal-label-status-sakit'),
            'izin' : document.getElementById('modal-label-status-izin'),
        };
        Object.entries(map).forEach(([val, lbl]) => {
            if (!lbl) return;
            const radio = lbl.querySelector('input[type="radio"]');
            const span  = lbl.querySelector('span');
            if (radio && radio.checked) {
                lbl.style.borderColor = 'var(--primary)';
                lbl.style.background  = 'var(--primary-bg, #e7f3ed)';
                if (span) span.style.color = 'var(--primary)';
            } else {
                lbl.style.borderColor = 'var(--line)';
                lbl.style.background  = '#fff';
                if (span) span.style.color = 'var(--ink-soft)';
            }
        });
    }

    function updateModalRequirements() {
        const modalStatusRadios = document.querySelectorAll('input[name="status"]');
        if (!modalStatusRadios.length) return;
        const val = getModalSelectedStatus();
        const lokasi = document.getElementById('presensi-lokasi');
        const bukti = document.getElementById('presensi-bukti');
        const catatan = document.getElementById('presensi-catatan');
        const catatanInd = document.getElementById('modal-catatan-req-indicator');
        const reqStars = document.querySelectorAll('.modal-req-star');
        const buktiStar = document.getElementById('modal-bukti-req-star');

        if (val === 'hadir') {
            reqStars.forEach(s => s.style.display = 'inline');
            if (buktiStar) buktiStar.style.display = 'inline';
            if (lokasi) lokasi.required = true;
            if (bukti) bukti.required = true;
            if (catatan) catatan.required = false;
            if (catatanInd) catatanInd.innerHTML = '<span style="font-weight:400; color:var(--muted);">(Opsional)</span>';
        } else if (val === 'izin' || val === 'sakit') {
            reqStars.forEach(s => s.style.display = 'none');
            if (buktiStar) buktiStar.style.display = 'inline';
            if (lokasi) lokasi.required = false;
            if (bukti) bukti.required = true;
            if (catatan) catatan.required = true;
            if (catatanInd) catatanInd.innerHTML = '<span style="color:var(--danger); font-weight:600;">* Wajib diisi alasan ' + val + '</span>';
        }
        applyModalStatusStyle();
    }

    document.addEventListener('DOMContentLoaded', () => {
        // -- Jam otomatis dari perangkat --
        function getNowTime() {
            const now = new Date();
            return String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
        }

        // -- Jenis Presensi: Datang / Pulang --
        const jenisDatang = document.getElementById('modal-jenis-datang');
        const jenisPulang = document.getElementById('modal-jenis-pulang');
        const labelDatang = document.getElementById('modal-label-datang');
        const labelPulang = document.getElementById('modal-label-pulang');
        const jamMasukInput = document.getElementById('presensi-jam-masuk');
        const fieldJamMasuk = document.getElementById('modal-field-jam-masuk');
        const fieldJamPulang = document.getElementById('modal-field-jam-pulang');

        window.applyModalJenis = function() {
            if (!jenisDatang) return;
            const isPulang = jenisPulang && jenisPulang.checked;
            const lokasi = document.getElementById('presensi-lokasi');
            const bukti = document.getElementById('presensi-bukti');
            const catatan = document.getElementById('presensi-catatan');
            const modalStatusRadios = document.querySelectorAll('input[name="status"]');

            if (!isPulang) {
                // Mode Datang
                labelDatang.style.borderColor = 'var(--primary)';
                labelDatang.style.background  = 'var(--primary-bg, #e7f3ed)';
                labelDatang.querySelector('span').style.color = 'var(--primary)';
                labelPulang.style.borderColor = 'var(--line)';
                labelPulang.style.background  = '#fff';
                labelPulang.querySelector('span').style.color = 'var(--ink-soft)';
                jamMasukInput.value  = getNowTime();
                jamPulangInput.value = '';
                if (fieldJamMasuk) fieldJamMasuk.style.display = 'block';
                if (fieldJamPulang) fieldJamPulang.style.display = 'none';
                jamPulangInput.required = false;
                modalStatusRadios.forEach(r => r.required = true);
                updateModalRequirements();
            } else {
                // Mode Pulang
                labelPulang.style.borderColor = 'var(--primary)';
                labelPulang.style.background  = 'var(--primary-bg, #e7f3ed)';
                labelPulang.querySelector('span').style.color = 'var(--primary)';
                labelDatang.style.borderColor = 'var(--line)';
                labelDatang.style.background  = '#fff';
                labelDatang.querySelector('span').style.color = 'var(--ink-soft)';
                jamMasukInput.value  = '';
                jamPulangInput.value = getNowTime();
                if (fieldJamMasuk) fieldJamMasuk.style.display = 'none';
                if (fieldJamPulang) fieldJamPulang.style.display = 'block';
                jamMasukInput.required = false;
                jamPulangInput.required = true;
                const modalStatusRadios2 = document.querySelectorAll('input[name="status"]');
                modalStatusRadios2.forEach(r => r.required = false);
                if (lokasi) lokasi.required = false;
                if (bukti) bukti.required = false;
                if (catatan) catatan.required = false;
            }
        };

        if (jenisDatang && jenisPulang) {
            // Set jam masuk otomatis saat pertama kali
            jamMasukInput.value = getNowTime();
            jenisDatang.addEventListener('change', applyModalJenis);
            jenisPulang.addEventListener('change', applyModalJenis);
            applyModalJenis();
        }

        // Status Change in Modal (radio buttons)
        const modalStatusRadios = document.querySelectorAll('input[name="status"]');
        modalStatusRadios.forEach(r => r.addEventListener('change', updateModalRequirements));
        applyModalStatusStyle();

        // File dropzone preview in modal
        const buktiInput = document.getElementById('presensi-bukti');
        const previewBox = document.getElementById('modal-preview-box');
        const previewImg = document.getElementById('modal-preview-img');
        const previewName = document.getElementById('modal-preview-name');
        const dzFilename = document.getElementById('modal-dz-filename');

        if (buktiInput) {
            buktiInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewBox.style.display = 'block';
                        previewName.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                    };
                    reader.readAsDataURL(file);

                    if (dzFilename) {
                        dzFilename.style.display = 'inline-flex';
                        dzFilename.innerHTML = `✓ ${file.name}`;
                    }
                }
            });
        }

        // Detect Location button in Modal (hanya isi lokasi, jam sudah otomatis)
        const btnDetect = document.getElementById('modal-btn-detect');
        const lokasiInput = document.getElementById('presensi-lokasi');

        if (btnDetect) {
            btnDetect.addEventListener('click', () => {
                if (!navigator.geolocation) {
                    alert('Geolocation tidak didukung pada browser ini.');
                    return;
                }
                const origHtml = btnDetect.innerHTML;
                btnDetect.disabled = true;
                btnDetect.innerHTML = '<span>Mendeteksi...</span>';

                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const lat = pos.coords.latitude.toFixed(5);
                        const lng = pos.coords.longitude.toFixed(5);
                        if (lokasiInput) lokasiInput.value = `Lokasi Kantor (GPS: ${lat}, ${lng})`;
                        btnDetect.disabled = false;
                        btnDetect.innerHTML = '<span>✓ Terdeteksi</span>';
                        setTimeout(() => { btnDetect.innerHTML = origHtml; }, 3000);
                    },
                    (err) => {
                        if (lokasiInput) lokasiInput.value = 'Kantor Bank Syariah Indonesia';
                        btnDetect.disabled = false;
                        btnDetect.innerHTML = origHtml;
                    },
                    { timeout: 8000, enableHighAccuracy: true }
                );
            });
        }

        // Close on clicking overlay outside the modal-box
        if (presensiModal) {
            presensiModal.addEventListener('click', (e) => {
                if (e.target === presensiModal) {
                    closePresensiModal();
                }
            });
        }

        if (photoModal) {
            photoModal.addEventListener('click', (e) => {
                if (e.target === photoModal) {
                    closePhotoModal();
                }
            });
        }

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' || e.key === 'Esc') {
                if (presensiModal && presensiModal.classList.contains('active')) {
                    closePresensiModal();
                }
                if (photoModal && photoModal.classList.contains('active')) {
                    closePhotoModal();
                }
            }
        });

        // Form submission with FormData AJAX & client validation
        if (presensiForm) {
            presensiForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                hidePresensiError();

                const isPulangMode = document.getElementById('modal-jenis-pulang')?.checked;
                const tanggalVal = document.getElementById('presensi-tanggal')?.value;
                const statusChecked = document.querySelector('input[name="status"]:checked');
                const statusVal = statusChecked ? statusChecked.value : 'hadir';
                const jamMasukVal = document.getElementById('presensi-jam-masuk')?.value;
                const jamPulangVal = document.getElementById('presensi-jam-pulang')?.value;
                const lokasiVal = document.getElementById('presensi-lokasi')?.value;
                const buktiFile = document.getElementById('presensi-bukti')?.files[0];
                const catatanVal = document.getElementById('presensi-catatan')?.value;

                // Client-side strict validation
                if (!tanggalVal) {
                    showPresensiError('<strong>Form Belum Lengkap:</strong> Tanggal presensi wajib diisi.');
                    return;
                }

                if (isPulangMode) {
                    if (!jamPulangVal) {
                        showPresensiError('<strong>Form Belum Lengkap:</strong> Jam pulang wajib terisi.');
                        return;
                    }
                } else {
                    // Mode Datang
                    if (statusVal === 'hadir') {
                        if (!jamMasukVal) {
                            showPresensiError('<strong>Form Belum Lengkap:</strong> Jam masuk wajib diisi untuk status Hadir.');
                            return;
                        }
                        if (!lokasiVal || lokasiVal.trim() === '') {
                            showPresensiError('<strong>Form Belum Lengkap:</strong> Lokasi kegiatan wajib diisi untuk status Hadir.');
                            return;
                        }
                        if (!buktiFile) {
                            showPresensiError('<strong>Bukti Foto Diperlukan:</strong> Foto bukti presensi yang memuat waktu & lokasi wajib diunggah.');
                            return;
                        }
                    } else if (statusVal === 'izin' || statusVal === 'sakit') {
                        if (!catatanVal || catatanVal.trim() === '') {
                            showPresensiError(`<strong>Form Belum Lengkap:</strong> Catatan keterangan / alasan ${statusVal} wajib diisi.`);
                            return;
                        }
                        if (!buktiFile) {
                            showPresensiError(`<strong>Bukti Foto Diperlukan:</strong> Lampiran bukti surat / foto untuk status ${statusVal} wajib diunggah.`);
                            return;
                        }
                    }

                    // Client validation: Jam Masuk < Jam Pulang jika keduanya diisi
                    if (jamMasukVal && jamPulangVal && jamMasukVal >= jamPulangVal) {
                        showPresensiError('<strong>Validasi Jam Gagal:</strong> Jam masuk harus lebih awal daripada jam pulang (' + jamMasukVal + ' &ge; ' + jamPulangVal + ').');
                        return;
                    }

                    // Client validation: Double presensi check on existing rows HANYA untuk Mode Datang
                    const existingRows = document.querySelectorAll('#presensi-tbody tr');
                    for (let row of existingRows) {
                        const rowTanggal = row.getAttribute('data-tanggal');
                        if (rowTanggal === tanggalVal) {
                            const dateFormatted = tanggalVal.split('-').reverse().join('-');
                            showPresensiError(`<strong>Presensi Ganda:</strong> Anda sudah mengisi presensi pada tanggal ${dateFormatted}. Tidak dapat mengisi presensi ganda pada tanggal yang sama.`);
                            return;
                        }
                    }
                }

                // Disable submit button & show loading state
                if (presensiSubmitBtn) {
                    presensiSubmitBtn.disabled = true;
                    if (presensiBtnLabel) presensiBtnLabel.textContent = 'Mengunggah & Menyimpan...';
                }

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

                    const formData = new FormData(presensiForm);

                    const response = await fetch(presensiForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        let errorMsg = result.message || 'Gagal menyimpan presensi.';
                        if (result.errors) {
                            const errList = Object.values(result.errors).flat().join('<br>');
                            errorMsg += `<div style="margin-top:4px;">${errList}</div>`;
                        }
                        showPresensiError(errorMsg);
                        return;
                    }

                    const d = result.data;
                    const topbarActions = document.querySelector('.topbar-actions');

                    if (result.is_pulang_update) {
                        // UPDATE baris yang sudah ada di tabel
                        const existingRow = document.querySelector(`#presensi-tbody tr[data-tanggal="${d.tanggal}"]`) ||
                                            document.querySelector(`#presensi-tbody tr[data-id="${d.id}"]`);
                        if (existingRow) {
                            const cells = existingRow.querySelectorAll('td');
                            if (cells.length >= 4) {
                                cells[3].textContent = d.jam_pulang;
                            }
                            existingRow.style.animation = 'none';
                            existingRow.offsetHeight; // trigger reflow
                            existingRow.style.animation = 'fadeInUp 0.4s ease';
                        }

                        // Update dataset modal
                        if (presensiModal) {
                            presensiModal.dataset.sudahDatang = '0';
                        }

                        // Update tombol topbar menjadi selesai
                        if (topbarActions) {
                            topbarActions.innerHTML = `<span class="badge badge-ok" style="background:#e7f3ed; color:var(--green); font-weight:700;">Presensi Hari Ini: Selesai</span>`;
                        }

                        closePresensiModal();
                        showToastNotification(result.message || 'Jam pulang berhasil dicatat.');
                    } else {
                        // INSERT baris baru secara dinamis
                        let tbody = document.getElementById('presensi-tbody');
                        const emptyState = document.getElementById('presensi-empty-state');

                        if (emptyState) {
                            const tableContainer = document.getElementById('presensi-table-container');
                            tableContainer.innerHTML = `
                                <div class="table-responsive">
                                    <table id="presensi-table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Status</th>
                                                <th>Jam Masuk</th>
                                                <th>Jam Pulang</th>
                                                <th>Lokasi</th>
                                                <th>Bukti Foto</th>
                                                <th>Mahasiswa</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="presensi-tbody"></tbody>
                                    </table>
                                </div>
                            `;
                            tbody = document.getElementById('presensi-tbody');
                        }

                        let badgeClass = 'badge-ok';
                        if (d.status === 'izin' || d.status === 'sakit') badgeClass = 'badge-wait';
                        else if (d.status === 'alpha') badgeClass = 'badge-danger';

                        const photoBtnHtml = d.bukti_url
                            ? `<button type="button" class="btn btn-ghost btn-sm" style="padding:4px 10px; font-size:12px;" onclick="viewPhotoModal('${d.bukti_url}', '${d.tanggal_formatted}', '${d.user_name}', '${d.status_upper}', '${d.lokasi}', '${d.jam_masuk}', '${d.catatan || '-'}')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    <span>Lihat Foto</span>
                               </button>`
                            : `<span style="color:var(--muted); font-size:12px;">-</span>`;

                        const newRow = document.createElement('tr');
                        newRow.setAttribute('data-id', d.id);
                        newRow.setAttribute('data-tanggal', d.tanggal);
                        newRow.style.animation = 'fadeInUp 0.4s ease';
                        newRow.innerHTML = `
                            <td>${d.tanggal_formatted}</td>
                            <td><span class="badge ${badgeClass}">${d.status_upper}</span></td>
                            <td>${d.jam_masuk}</td>
                            <td>${d.jam_pulang}</td>
                            <td>${d.lokasi}</td>
                            <td>${photoBtnHtml}</td>
                            <td><strong>${d.user_name}</strong></td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <a class="btn btn-ghost btn-sm" href="${d.edit_url}">Edit</a>
                                    <form action="${d.destroy_url}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus catatan presensi ini?" data-title="Hapus Presensi">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        `;

                        tbody.prepend(newRow);

                        // Update modal state & topbar
                        if (d.status === 'hadir') {
                            if (presensiModal) {
                                presensiModal.dataset.sudahDatang = '1';
                                presensiModal.dataset.presensiId = d.id;
                                presensiModal.dataset.jamMasuk = d.jam_masuk;
                            }
                            if (topbarActions) {
                                topbarActions.innerHTML = `
                                    <button type="button" class="btn btn-solid" onclick="openPresensiModal()" style="background:var(--amber, #f59e0b);">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <span>Presensi Pulang</span>
                                    </button>
                                `;
                            }
                        } else {
                            if (topbarActions) {
                                topbarActions.innerHTML = `<span class="badge badge-ok" style="background:#e7f3ed; color:var(--green); font-weight:700;">Presensi Hari Ini: Selesai</span>`;
                            }
                        }

                        closePresensiModal();
                        showToastNotification(result.message || 'Presensi berhasil disimpan.');
                    }

                } catch (err) {
                    showPresensiError('Terjadi kesalahan saat memproses data/mengunggah berkas. Silakan coba kembali.');
                } finally {
                    if (presensiSubmitBtn) {
                        presensiSubmitBtn.disabled = false;
                        if (presensiBtnLabel) presensiBtnLabel.textContent = 'Simpan Presensi';
                    }
                }
            });
        }

        // Auto-open modal if URL has ?action=create
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'create') {
            openPresensiModal();
        }
    });
</script>
@endsection

