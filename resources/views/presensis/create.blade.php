@extends('layouts.app')

@section('title', 'SIMMM - Form Presensi')

@section('content')
<x-topbar :title="isset($presensi) ? 'Edit Presensi' : 'Input Presensi Harian'" subtitle="Isi data kehadiran, lokasi kegiatan, dan unggah foto bukti presensi yang memiliki watermark waktu & lokasi." />

<div class="card" style="max-width:780px;">
    <div class="card-header">
        <div>
            <h3>Formulir Kehadiran Harian</h3>
            <div class="card-sub">Lengkapi seluruh kolom formulir sebelum menyimpan presensi.</div>
        </div>
    </div>

    @if ($errors->any())
        <div style="background:var(--danger-bg); color:var(--danger); border:1px solid #efc0b9; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13.5px;">
            <div style="font-weight:700; margin-bottom:6px; display:flex; align-items:center; gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Terdapat kesalahan pada input formulir:
            </div>
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Notice Box: Ketentuan Foto Bukti Presensi --}}
    <div style="background:linear-gradient(135deg, #e7f3ed 0%, #d4e5e0 100%); border:1px solid #bce3d0; border-radius:10px; padding:16px 18px; margin-bottom:24px; display:flex; gap:14px; align-items:flex-start;">
        <div style="width:36px; height:36px; border-radius:8px; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        </div>
        <div style="font-size:13px; line-height:1.5; color:var(--ink-soft);">
            <div style="font-weight:700; color:var(--primary-dark); font-size:14px; margin-bottom:3px;">
                Ketentuan Foto Bukti Presensi (Wajib Memuat Waktu & Lokasi)
            </div>
            Foto kehadiran wajib berupa <strong>selfie / dokumentasi di lingkungan kantor BSI</strong> yang memuat <strong>watermark Waktu (Tanggal & Jam) dan Lokasi (Geotag/GPS)</strong> secara jelas (dapat menggunakan aplikasi kamera <em>Timestamp Camera</em>, <em>GPS Map Camera</em>, atau fitur watermark kamera smartphone Anda).
        </div>
    </div>

    <form method="POST" action="{{ isset($presensi) ? route('presensis.update', $presensi) : route('presensis.store') }}" enctype="multipart/form-data" id="presensi-form-page">
        @csrf
        @if(isset($presensi))
            @method('PUT')
        @endif

        {{-- Jenis Presensi: Datang / Pulang --}}
        @if(!isset($presensi))
        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:8px;">Jenis Presensi <span style="color:var(--danger);">*</span></label>
            <div style="display:flex; gap:12px;">
                <label id="label-jenis-datang" style="flex:1; display:flex; align-items:center; gap:10px; border:2px solid var(--primary); border-radius:8px; padding:12px 16px; cursor:pointer; background:var(--primary-bg, #e7f3ed); transition:all .2s;">
                    <input type="radio" name="jenis_presensi" id="jenis-datang" value="datang" checked style="accent-color:var(--primary); width:16px; height:16px;">
                    <span style="font-size:14px; font-weight:600; color:var(--primary);">Presensi Masuk</span>
                </label>
                <label id="label-jenis-pulang" style="flex:1; display:flex; align-items:center; gap:10px; border:2px solid var(--line); border-radius:8px; padding:12px 16px; cursor:pointer; background:#fff; transition:all .2s;">
                    <input type="radio" name="jenis_presensi" id="jenis-pulang" value="pulang" style="accent-color:var(--primary); width:16px; height:16px;">
                    <span style="font-size:14px; font-weight:600; color:var(--ink-soft);">Presensi Pulang</span>
                </label>
            </div>
        </div>
        @endif

        {{-- Status Kehadiran: Radio Button Group --}}
        @php $selectedStatus = old('status', $presensi->status ?? 'hadir'); @endphp
        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:8px;">Status Kehadiran <span style="color:var(--danger);">*</span></label>
            <div style="display:flex; gap:10px; flex-wrap:wrap;" role="radiogroup" aria-label="Status Kehadiran">
                <label id="label-status-hadir" style="flex:1; min-width:100px; display:flex; align-items:center; gap:9px; border:2px solid {{ $selectedStatus === 'hadir' ? 'var(--primary)' : 'var(--line)' }}; border-radius:8px; padding:10px 14px; cursor:pointer; background:{{ $selectedStatus === 'hadir' ? 'var(--primary-bg, #e7f3ed)' : '#fff' }}; transition:all .2s;">
                    <input type="radio" name="status" id="status-hadir" value="hadir" {{ $selectedStatus === 'hadir' ? 'checked' : '' }} style="accent-color:var(--primary); width:15px; height:15px; flex-shrink:0;">
                    <span style="font-size:13.5px; font-weight:600; color:{{ $selectedStatus === 'hadir' ? 'var(--primary)' : 'var(--ink-soft)' }};">Hadir</span>
                </label>
                <label id="label-status-sakit" style="flex:1; min-width:100px; display:flex; align-items:center; gap:9px; border:2px solid {{ $selectedStatus === 'sakit' ? 'var(--primary)' : 'var(--line)' }}; border-radius:8px; padding:10px 14px; cursor:pointer; background:{{ $selectedStatus === 'sakit' ? 'var(--primary-bg, #e7f3ed)' : '#fff' }}; transition:all .2s;">
                    <input type="radio" name="status" id="status-sakit" value="sakit" {{ $selectedStatus === 'sakit' ? 'checked' : '' }} style="accent-color:var(--primary); width:15px; height:15px; flex-shrink:0;">
                    <span style="font-size:13.5px; font-weight:600; color:{{ $selectedStatus === 'sakit' ? 'var(--primary)' : 'var(--ink-soft)' }};">Sakit</span>
                </label>
                <label id="label-status-izin" style="flex:1; min-width:100px; display:flex; align-items:center; gap:9px; border:2px solid {{ $selectedStatus === 'izin' ? 'var(--primary)' : 'var(--line)' }}; border-radius:8px; padding:10px 14px; cursor:pointer; background:{{ $selectedStatus === 'izin' ? 'var(--primary-bg, #e7f3ed)' : '#fff' }}; transition:all .2s;">
                    <input type="radio" name="status" id="status-izin" value="izin" {{ $selectedStatus === 'izin' ? 'checked' : '' }} style="accent-color:var(--primary); width:15px; height:15px; flex-shrink:0;">
                    <span style="font-size:13.5px; font-weight:600; color:{{ $selectedStatus === 'izin' ? 'var(--primary)' : 'var(--ink-soft)' }};">Izin</span>
                </label>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:18px;">
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Tanggal Presensi <span style="color:var(--danger);">*</span></label>
                <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', isset($presensi) ? \Illuminate\Support\Carbon::parse($presensi->tanggal)->format('Y-m-d') : now()->format('Y-m-d')) }}" required style="width:100%; border:1px solid var(--line); border-radius:6px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif;">
                @error('tanggal')
                    <div style="color:var(--danger); font-size:12px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Jam Masuk: otomatis dari jam browser, readonly --}}
            <div id="wrapper-jam-masuk">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Jam Masuk <span style="color:var(--danger);" class="req-star">*</span></label>
                <div style="position:relative;">
                    <input type="time" name="jam_masuk" id="jam-masuk"
                        value="{{ old('jam_masuk', isset($presensi) && $presensi->jam_masuk ? \Illuminate\Support\Carbon::parse($presensi->jam_masuk)->format('H:i') : '') }}"
                        readonly
                        style="width:100%; border:1px solid var(--line); border-radius:6px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif; background:#f5f5f5; cursor:not-allowed;">
                    <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:11px; color:var(--muted); pointer-events:none;">Otomatis</span>
                </div>
                <div style="font-size:11.5px; color:var(--muted); margin-top:3px;">Jam masuk terisi otomatis sesuai waktu perangkat Anda.</div>
                @error('jam_masuk')
                    <div style="color:var(--danger); font-size:12px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Jam Pulang: otomatis dari jam browser, readonly --}}
            <div id="wrapper-jam-pulang">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Jam Pulang <span style="font-weight:400; color:var(--muted);">(Opsional)</span></label>
                <div style="position:relative;">
                    <input type="time" name="jam_pulang" id="jam-pulang"
                        value="{{ old('jam_pulang', isset($presensi) && $presensi->jam_pulang ? \Illuminate\Support\Carbon::parse($presensi->jam_pulang)->format('H:i') : '') }}"
                        readonly
                        style="width:100%; border:1px solid var(--line); border-radius:6px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif; background:#f5f5f5; cursor:not-allowed;">
                    <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:11px; color:var(--muted); pointer-events:none;">Otomatis</span>
                </div>
                <div style="font-size:11.5px; color:var(--muted); margin-top:3px;">Jam pulang terisi otomatis saat Anda pilih Presensi Pulang.</div>
                @error('jam_pulang')
                    <div style="color:var(--danger); font-size:12px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="margin-bottom:18px;" id="wrapper-lokasi">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                <label style="font-size:13px; font-weight:600; margin:0;">Lokasi Kegiatan <span style="color:var(--danger);" class="req-star">*</span></label>
                <button type="button" id="btn-detect-location" class="btn btn-ghost btn-sm" style="padding:4px 10px; font-size:11.5px; height:auto; display:inline-flex; gap:4px; align-items:center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>Deteksi Lokasi &amp; Waktu Saya</span>
                </button>
            </div>
            <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $presensi->lokasi ?? '') }}" placeholder="Contoh: BSI Area Yogyakarta / WFH" style="width:100%; border:1px solid var(--line); border-radius:6px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif;">
            <div id="location-hint" style="font-size:11.5px; color:var(--muted); margin-top:4px;">Klik 'Deteksi Lokasi & Waktu' jika ingin mengisi otomatis koordinat/lokasi perangkat saat ini.</div>
            @error('lokasi')
                <div style="color:var(--danger); font-size:12px; margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Upload Bukti Foto Presensi (File Input) --}}
        <div style="margin-bottom:20px;" id="wrapper-bukti">
            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">
                Foto Bukti Presensi <span style="color:var(--danger);" id="bukti-req-star">*</span>
                <span style="font-size:12px; font-weight:400; color:var(--muted);">(Format: JPG, PNG, WEBP, Maks. 5MB)</span>
            </label>

            <div class="file-dropzone" id="dropzone-page" style="padding:22px 18px; border-radius:8px;">
                <input type="file" name="bukti" id="bukti-input-page" accept="image/*" {{ isset($presensi) && $presensi->bukti_path ? '' : 'required' }}>
                <div class="dz-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                <div class="dz-title" id="dz-title-page">Pilih atau Tarik Foto Bukti Presensi di Sini</div>
                <div class="dz-sub">Pastikan foto memiliki watermark tanggal, jam, dan lokasi yang jelas</div>
                <div id="file-info-page" class="dz-filename" style="display:none;"></div>
            </div>

            @error('bukti')
                <div style="color:var(--danger); font-size:12px; margin-top:6px;">{{ $message }}</div>
            @enderror

            {{-- Preview Gambar --}}
            <div id="image-preview-container" style="{{ isset($presensi) && $presensi->bukti_path ? 'display:block;' : 'display:none;' }} margin-top:14px; padding:12px; background:var(--bg); border:1px solid var(--line); border-radius:8px;">
                <div style="font-size:12px; font-weight:600; color:var(--ink-soft); margin-bottom:8px; display:flex; justify-content:space-between; align-items:center;">
                    <span>Pratinjau Foto Bukti:</span>
                    @if(isset($presensi) && $presensi->bukti_path)
                        <span style="color:var(--green); font-size:11.5px;">Foto saat ini tersimpan</span>
                    @endif
                </div>
                <div style="display:flex; gap:14px; align-items:center;">
                    <img id="preview-img" src="{{ isset($presensi) && $presensi->bukti_path ? asset('storage/' . $presensi->bukti_path) : '#' }}" alt="Pratinjau Foto" style="max-height:140px; border-radius:6px; object-fit:cover; border:1px solid var(--line); box-shadow:var(--shadow-sm);">
                    <div style="font-size:12px; color:var(--muted); line-height:1.4;">
                        <div>Status: <span id="preview-filename" style="font-weight:600; color:var(--ink);">{{ isset($presensi) && $presensi->bukti_path ? basename($presensi->bukti_path) : '' }}</span></div>
                        <div style="margin-top:4px; color:var(--primary); font-weight:500;">Foto siap diunggah dan diverifikasi pembimbing</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-bottom:24px;">
            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">
                Catatan / Keterangan <span id="catatan-req-indicator" style="font-weight:400; color:var(--muted);">(Opsional)</span>
            </label>
            <textarea name="catatan" id="catatan" placeholder="Catatan aktivitas harian atau alasan bila Izin/Sakit..." style="width:100%; border:1px solid var(--line); border-radius:6px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif; min-height:85px; resize:vertical;">{{ old('catatan', $presensi->catatan ?? '') }}</textarea>
            @error('catatan')
                <div style="color:var(--danger); font-size:12px; margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--line); padding-top:18px;">
            <a href="{{ route('presensis.index') }}" class="btn btn-ghost">Kembali ke Daftar</a>
            <button type="submit" class="btn btn-solid" id="submit-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <span id="submit-btn-label">{{ isset($presensi) ? 'Perbarui Presensi' : 'Simpan Presensi' }}</span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form            = document.getElementById('presensi-form-page');
    const submitBtn       = document.getElementById('submit-btn');
    const submitBtnLabel  = document.getElementById('submit-btn-label');

    // Mencegah double-submit dan menampilkan status loading
    if (form && submitBtn) {
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
            submitBtn.style.cursor = 'not-allowed';
            if (submitBtnLabel) submitBtnLabel.textContent = 'Menyimpan...';
        });
    }

    const statusRadios    = document.querySelectorAll('input[name="status"]');
    const jamMasukInput   = document.getElementById('jam-masuk');
    const jamPulangInput  = document.getElementById('jam-pulang');

    // Helper: dapatkan nilai radio status yang sedang terpilih
    function getSelectedStatus() {
        const checked = document.querySelector('input[name="status"]:checked');
        return checked ? checked.value : 'hadir';
    }

    // Helper: update style card radio status
    function applyStatusStyle() {
        const statusMap = {
            'hadir': document.getElementById('label-status-hadir'),
            'sakit': document.getElementById('label-status-sakit'),
            'izin' : document.getElementById('label-status-izin'),
        };
        Object.entries(statusMap).forEach(([val, lbl]) => {
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
    const lokasiInput     = document.getElementById('lokasi');
    const buktiInput      = document.getElementById('bukti-input-page');
    const catatanInput    = document.getElementById('catatan');
    const catatanReqIndicator = document.getElementById('catatan-req-indicator');
    const btnDetect       = document.getElementById('btn-detect-location');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg      = document.getElementById('preview-img');
    const previewFilename = document.getElementById('preview-filename');
    const fileInfoPage    = document.getElementById('file-info-page');
    const wrapperJamMasuk = document.getElementById('wrapper-jam-masuk');
    const wrapperJamPulang = document.getElementById('wrapper-jam-pulang');
    const isEditMode      = {{ isset($presensi) ? 'true' : 'false' }};
    const hasExistingBukti = {{ isset($presensi) && $presensi->bukti_path ? 'true' : 'false' }};

    // -- Jam otomatis dari perangkat (readonly) --
    function getNowTime() {
        const now = new Date();
        return String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
    }

    // -- Jenis Presensi: Datang / Pulang --
    const jenisDatang = document.getElementById('jenis-datang');
    const jenisPulang = document.getElementById('jenis-pulang');
    const labelDatang = document.getElementById('label-jenis-datang');
    const labelPulang = document.getElementById('label-jenis-pulang');

    function applyJenisStyle() {
        if (!jenisDatang) return;
        if (jenisDatang.checked) {
            labelDatang.style.borderColor = 'var(--primary)';
            labelDatang.style.background  = 'var(--primary-bg, #e7f3ed)';
            labelDatang.querySelector('span').style.color = 'var(--primary)';
            labelPulang.style.borderColor = 'var(--line)';
            labelPulang.style.background  = '#fff';
            labelPulang.querySelector('span').style.color = 'var(--ink-soft)';

            // Mode Datang: isi jam_masuk, kosongkan jam_pulang
            jamMasukInput.value  = getNowTime();
            jamPulangInput.value = '';

            // Tampilkan wrapper jam masuk, sembunyikan jam pulang
            if (wrapperJamMasuk) wrapperJamMasuk.style.display = 'block';
            if (wrapperJamPulang) wrapperJamPulang.style.display = 'none';
        } else {
            labelPulang.style.borderColor = 'var(--primary)';
            labelPulang.style.background  = 'var(--primary-bg, #e7f3ed)';
            labelPulang.querySelector('span').style.color = 'var(--primary)';
            labelDatang.style.borderColor = 'var(--line)';
            labelDatang.style.background  = '#fff';
            labelDatang.querySelector('span').style.color = 'var(--ink-soft)';

            // Mode Pulang: isi jam_pulang, biarkan jam_masuk kosong (tidak dikirim)
            jamMasukInput.value  = '';
            jamPulangInput.value = getNowTime();

            // Tampilkan wrapper jam pulang, sembunyikan jam masuk
            if (wrapperJamMasuk) wrapperJamMasuk.style.display = 'none';
            if (wrapperJamPulang) wrapperJamPulang.style.display = 'block';
        }
    }

    if (jenisDatang && jenisPulang) {
        // Set jam masuk otomatis saat pertama load (mode Datang)
        jamMasukInput.value = getNowTime();
        if (wrapperJamPulang) wrapperJamPulang.style.display = 'none';

        jenisDatang.addEventListener('change', applyJenisStyle);
        jenisPulang.addEventListener('change', applyJenisStyle);
        applyJenisStyle();
    } else if (isEditMode) {
        // Mode edit: biarkan jam yg sudah ada, readonly
        if (!jamMasukInput.value && !jamPulangInput.value) {
            jamMasukInput.value = getNowTime();
        }
    }

    // -- Update form requirements berdasarkan status --
    function updateFormRequirements() {
        const val = getSelectedStatus();
        const reqStars = document.querySelectorAll('.req-star');

        if (val === 'hadir') {
            reqStars.forEach(s => s.style.display = 'inline');
            lokasiInput.required = true;
            catatanInput.required = false;
            catatanReqIndicator.innerHTML = '<span style="font-weight:400; color:var(--muted);">(Opsional)</span>';
            if (!isEditMode || !hasExistingBukti) {
                buktiInput.required = true;
            }
        } else if (val === 'izin' || val === 'sakit') {
            reqStars.forEach(s => s.style.display = 'none');
            lokasiInput.required = false;
            catatanInput.required = true;
            catatanReqIndicator.innerHTML = '<span style="color:var(--danger); font-weight:600;">* Wajib diisi alasan ' + val + '</span>';
            if (!isEditMode || !hasExistingBukti) {
                buktiInput.required = true;
            }
        }
        applyStatusStyle();
    }

    if (statusRadios.length) {
        statusRadios.forEach(r => r.addEventListener('change', updateFormRequirements));
        applyStatusStyle();
        updateFormRequirements();
    }

    // -- Handle Image Preview --
    if (buktiInput) {
        buktiInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.style.display = 'block';
                    previewFilename.textContent = file.name + ` (${(file.size / 1024).toFixed(1)} KB)`;
                };
                reader.readAsDataURL(file);

                if (fileInfoPage) {
                    fileInfoPage.style.display = 'inline-flex';
                    fileInfoPage.innerHTML = `✓ Terpilih: ${file.name}`;
                }
            }
        });
    }

    // -- Geolocation Detection Button --
    if (btnDetect) {
        btnDetect.addEventListener('click', () => {
            if (!navigator.geolocation) {
                alert('Fitur Geolocation tidak didukung oleh browser Anda.');
                return;
            }

            const origHtml = btnDetect.innerHTML;
            btnDetect.disabled = true;
            btnDetect.innerHTML = '<span>Mendeteksi...</span>';

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude.toFixed(5);
                    const lng = pos.coords.longitude.toFixed(5);
                    lokasiInput.value = `BSI Area Yogyakarta (GPS: ${lat}, ${lng})`;
                    btnDetect.disabled = false;
                    btnDetect.innerHTML = '<span>✓ Lokasi Terdeteksi</span>';
                    setTimeout(() => { btnDetect.innerHTML = origHtml; }, 3000);
                },
                (err) => {
                    lokasiInput.value = 'BSI Area Yogyakarta';
                    btnDetect.disabled = false;
                    btnDetect.innerHTML = origHtml;
                    alert('Tidak dapat mendeteksi koordinat GPS otomatis (' + err.message + '). Lokasi disetel ke default BSI Area Yogyakarta.');
                },
                { timeout: 8000, enableHighAccuracy: true }
            );
        });
    }
});
</script>
@endsection
