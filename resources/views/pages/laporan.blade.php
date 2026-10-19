@extends('layouts.app')

@section('title', 'SIMMM - Laporan Akhir Magang')

@section('content')
<div class="topbar">
    <div>
        <h1>Laporan Akhir Magang</h1>
        <div class="subtitle">{{ $isAdmin ? 'Tinjau, periksa, dan verifikasi dokumen laporan akhir seluruh mahasiswa magang.' : 'Unggah dan pantau status verifikasi dokumen laporan akhir magang Anda di BSI.' }}</div>
    </div>
</div>

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
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
    </div>
@endif

@if(!$isAdmin)
    {{-- TAMPILAN MAHASISWA --}}

    {{-- Kartu Status Laporan Akhir (Tampil jika sudah pernah upload) --}}
    @if($mahasiswa && $mahasiswa->laporan_akhir)
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <div>
                    <h3>Dokumen Laporan Akhir Terunggah</h3>
                    <div class="card-sub">Data laporan akhir resmi yang tersimpan di sistem.</div>
                </div>
                <div>
                    @if($mahasiswa->status_laporan_akhir === 'disetujui')
                        <span class="badge badge-ok">&bull; DISETUJUI</span>
                    @elseif($mahasiswa->status_laporan_akhir === 'revisi')
                        <span class="badge badge-danger">&bull; PERLU REVISI</span>
                    @else
                        <span class="badge badge-wait">&bull; MENUNGGU VERIFIKASI</span>
                    @endif
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px; padding:18px; background:var(--bg); border:1px solid var(--line); border-radius:10px; margin-bottom:20px;">
                <div>
                    <div style="font-size:12px; color:var(--muted); text-transform:uppercase; font-weight:600; letter-spacing:0.05em; margin-bottom:4px;">Nama File</div>
                    <div style="font-size:14px; font-weight:600; color:var(--ink); word-break:break-all;" id="current-filename">{{ $mahasiswa->nama_file_laporan ?: basename($mahasiswa->laporan_akhir) }}</div>
                </div>
                <div>
                    <div style="font-size:12px; color:var(--muted); text-transform:uppercase; font-weight:600; letter-spacing:0.05em; margin-bottom:4px;">Format &amp; Tipe</div>
                    <div style="font-size:14px; font-weight:600; color:var(--ink);">Dokumen PDF (.pdf)</div>
                </div>
                <div>
                    <div style="font-size:12px; color:var(--muted); text-transform:uppercase; font-weight:600; letter-spacing:0.05em; margin-bottom:4px;">Tanggal Unggah</div>
                    <div style="font-size:14px; font-weight:600; color:var(--ink);" id="current-upload-date">
                        {{ $mahasiswa->tanggal_laporan_akhir ? \Illuminate\Support\Carbon::parse($mahasiswa->tanggal_laporan_akhir)->isoFormat('D MMMM Y, HH:mm') . ' WIB' : '-' }}
                    </div>
                </div>
                <div>
                    <div style="font-size:12px; color:var(--muted); text-transform:uppercase; font-weight:600; letter-spacing:0.05em; margin-bottom:4px;">Aksi Dokumen</div>
                    <a href="{{ asset('storage/' . $mahasiswa->laporan_akhir) }}" target="_blank" class="btn btn-solid btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        Lihat / Unduh PDF
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Form Unggah Laporan Akhir (hanya tampil sebelum upload pertama) --}}
    @if(!$mahasiswa || !$mahasiswa->laporan_akhir)
    <div class="card" id="upload-section">
        <div class="card-header">
            <div>
                <h3>Formulir Unggah Laporan Akhir</h3>
                <div class="card-sub">Format dokumen harus PDF dengan ukuran file maksimal 5MB.</div>
            </div>
        </div>

        <form id="laporan-upload-form" action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            
            <div id="laporan-alert-error" style="display:none; background:var(--danger-bg); color:var(--danger); border:1px solid #efc0b9; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13.5px;">
                <div id="laporan-error-text"></div>
            </div>

            <div class="file-dropzone" id="laporan-dropzone" style="min-height:190px; cursor:pointer;">
                <input type="file" name="laporan_akhir" id="laporan-file-input" accept=".pdf,application/pdf" style="cursor:pointer;" oninvalid="this.setCustomValidity('Silakan pilih file dokumen PDF Laporan Akhir terlebih dahulu.')" oninput="this.setCustomValidity('')">
                <div class="dz-icon" style="color:var(--primary);">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                <div class="dz-title" style="font-size:16px;">Pilih File Dokumen Laporan Akhir <span style="color:var(--danger);">* Wajib PDF</span></div>
                <div class="dz-sub" style="font-size:13px; margin-top:4px;">Klik untuk memilih file PDF atau tarik dan lepas file di sini (Maksimal 5MB)</div>
                
                {{-- Preview Nama File yang dipilih --}}
                <div id="file-chosen-preview" style="display:none; margin-top:14px; padding:8px 14px; background:var(--green-bg); color:var(--green); border:1px solid #bce3d0; border-radius:6px; font-size:13px; font-weight:600; align-items:center; gap:8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span id="chosen-filename-text"></span>
                </div>
            </div>

            <div style="margin-top:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                <div style="font-size:12.5px; color:var(--muted);">
                    <span style="font-weight:600; color:var(--ink);">Ketentuan:</span> Dokumen harus mencakup lembar pengesahan, laporan kegiatan, serta dokumentasi magang.
                </div>
                <button type="submit" id="laporan-submit-btn" class="btn btn-solid">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span id="laporan-btn-text">Kirim Laporan Akhir</span>
                </button>
            </div>
        </form>
    </div>
    @endif

@else
    {{-- TAMPILAN ADMIN / MENTOR --}}
    <div class="card">
        <div class="card-header">
            <div>
                <h3>Verifikasi Laporan Akhir Mahasiswa</h3>
                <div class="card-sub">Daftar laporan akhir magang yang diunggah oleh mahasiswa.</div>
            </div>
        </div>

        @if($mahasiswas->isEmpty())
            <div class="empty-state">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <h3>Belum Ada Laporan Akhir</h3>
                <p>Belum ada mahasiswa yang mengunggah laporan akhir magang.</p>
            </div>
        @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Mahasiswa &amp; NIM</th>
                        <th>Perguruan Tinggi &amp; Cabang/Unit</th>
                        <th>Dokumen Laporan</th>
                        <th>Tanggal Unggah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mahasiswas as $mhs)
                        <tr>
                            <td>
                                <strong>{{ $mhs->nama_lengkap }}</strong>
                                <div style="font-size:12px; color:var(--muted);">NIM: {{ $mhs->nim }}</div>
                            </td>
                            <td>
                                <div>{{ $mhs->universitas }}</div>
                                <div style="font-size:12px; color:var(--primary); font-weight:600;">Cabang/Unit Penempatan: {{ $mhs->cabang?->nama ?: 'Belum ditentukan' }}</div>
                            </td>
                            <td>
                                @if($mhs->laporan_akhir)
                                    <div style="font-weight:600; font-size:13px; color:var(--ink); word-break:break-all;">{{ $mhs->nama_file_laporan ?: 'Laporan_Akhir.pdf' }}</div>
                                    <a href="{{ asset('storage/' . $mhs->laporan_akhir) }}" target="_blank" style="font-size:12px; color:var(--primary); text-decoration:underline; display:inline-flex; align-items:center; gap:4px; margin-top:3px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                        Buka Dokumen PDF
                                    </a>
                                @else
                                    <span style="font-size:12.5px; color:var(--muted); font-style:italic;">Belum mengunggah</span>
                                @endif
                            </td>
                            <td>
                                {{ $mhs->tanggal_laporan_akhir ? \Illuminate\Support\Carbon::parse($mhs->tanggal_laporan_akhir)->format('d-m-Y H:i') : '-' }}
                            </td>
                            <td>
                                @if(!$mhs->laporan_akhir)
                                    <span class="badge" style="background:var(--line); color:var(--muted);">BELUM UNGGAH</span>
                                @elseif($mhs->status_laporan_akhir === 'disetujui')
                                    <span class="badge badge-ok">DISETUJUI</span>
                                @elseif($mhs->status_laporan_akhir === 'revisi')
                                    <span class="badge badge-danger">REVISI</span>
                                @else
                                    <span class="badge badge-wait">MENUNGGU VERIFIKASI</span>
                                @endif
                            </td>
                            <td>
                                @if($mhs->laporan_akhir)
                                    <button type="button" class="btn btn-solid btn-sm" onclick="openVerifikasiLaporanModal({{ $mhs->id }}, '{{ e($mhs->nama_lengkap) }}', '{{ $mhs->status_laporan_akhir }}', '{{ e($mhs->catatan_laporan_akhir) }}', '{{ route('laporan.verifikasi', $mhs->id) }}')">
                                        Verifikasi
                                    </button>
                                @else
                                    <span style="font-size:12px; color:var(--muted);">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($mahasiswas->hasPages())
            <div style="margin-top:20px; display:flex; justify-content:center;">
                {{ $mahasiswas->links() }}
            </div>
        @endif
    @endif
    </div>

    {{-- Modal Verifikasi Laporan Admin --}}
    <div id="verifikasi-laporan-modal" class="modal-overlay" aria-hidden="true">
        <div class="modal-box" style="max-width:500px;" role="dialog" aria-modal="true" aria-labelledby="modal-lap-title">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--line); padding-bottom:14px;">
                <div id="modal-lap-title" class="modal-title" style="margin:0; font-size:18px; font-weight:700;">Verifikasi Laporan Akhir</div>
                <button type="button" onclick="closeVerifikasiLaporanModal()" aria-label="Tutup modal" style="background:none; border:none; cursor:pointer; color:var(--muted); font-size:24px; line-height:1; padding:0 4px;">&times;</button>
            </div>

            <form id="verifikasi-laporan-form" method="POST" action="">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom:16px;">
                    <div style="font-size:13px; color:var(--muted); margin-bottom:4px;">Mahasiswa:</div>
                    <div style="font-size:15px; font-weight:700; color:var(--ink);" id="modal-lap-nama">-</div>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Keputusan Verifikasi <span style="color:var(--danger);">*</span></label>
                    <select name="status_laporan_akhir" id="modal-lap-status" required style="width:100%; border:1px solid var(--line); border-radius:6px; padding:10px 12px; font-size:14px; font-family:'Inter',sans-serif; background:#fff;">
                        <option value="disetujui">✓ Setujui Laporan Akhir</option>
                        <option value="revisi">↩ Minta Revisi Laporan</option>
                    </select>
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Catatan / Masukan Mentor <span style="font-weight:400; color:var(--muted);">(Wajib jika meminta revisi)</span></label>
                    <textarea name="catatan_laporan_akhir" id="modal-lap-catatan" placeholder="Tuliskan catatan revisi atau instruksi perbaikan..." style="width:100%; border:1px solid var(--line); border-radius:6px; padding:10px 12px; font-size:13.5px; font-family:'Inter',sans-serif; min-height:85px; resize:vertical;"></textarea>
                </div>

                <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--line); padding-top:16px;">
                    <button type="button" class="btn btn-ghost" onclick="closeVerifikasiLaporanModal()">Batal</button>
                    <button type="submit" class="btn btn-solid">Simpan Keputusan</button>
                </div>
            </form>
        </div>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('laporan-file-input');
        const previewEl = document.getElementById('file-chosen-preview');
        const filenameText = document.getElementById('chosen-filename-text');
        const alertBox = document.getElementById('laporan-alert-error');
        const errorText = document.getElementById('laporan-error-text');
        const uploadForm = document.getElementById('laporan-upload-form');
        const submitBtn = document.getElementById('laporan-submit-btn');
        const btnText = document.getElementById('laporan-btn-text');
        const dropzone = document.getElementById('laporan-dropzone');

        function showError(msg) {
            if (!alertBox || !errorText) return;
            errorText.innerHTML = msg;
            alertBox.style.display = 'block';
            if (dropzone) {
                dropzone.style.borderColor = 'var(--danger)';
            }
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function hideError() {
            if (!alertBox) return;
            alertBox.style.display = 'none';
            if (errorText) errorText.innerHTML = '';
            if (dropzone) {
                dropzone.style.borderColor = 'var(--line)';
            }
        }

        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                hideError();
                const files = e.target.files;
                if (!files || files.length === 0) {
                    if (previewEl) previewEl.style.display = 'none';
                    return;
                }

                const file = files[0];
                const fileName = file.name;
                const fileSize = file.size; // in bytes
                const fileExt = fileName.split('.').pop().toLowerCase();

                // Validasi format PDF
                if (fileExt !== 'pdf' && file.type !== 'application/pdf') {
                    showError('<strong>Format File Tidak Sesuai:</strong> Berkas harus berformat PDF (.pdf). Format berkas yang Anda pilih adalah <code>.' + fileExt + '</code>');
                    fileInput.value = '';
                    if (previewEl) previewEl.style.display = 'none';
                    return;
                }

                // Validasi ukuran maks 5MB
                const maxBytes = 5 * 1024 * 1024;
                if (fileSize > maxBytes) {
                    const sizeMB = (fileSize / (1024 * 1024)).toFixed(2);
                    showError('<strong>Ukuran Berkas Terlalu Besar:</strong> Ukuran file (' + sizeMB + ' MB) melebihi batas maksimal yang diizinkan (5.00 MB).');
                    fileInput.value = '';
                    if (previewEl) previewEl.style.display = 'none';
                    return;
                }

                // Tampilkan nama file dan ukuran setelah berhasil dipilih
                const sizeFormatted = fileSize >= 1048576 
                    ? (fileSize / (1024 * 1024)).toFixed(2) + ' MB'
                    : Math.round(fileSize / 1024) + ' KB';

                if (previewEl && filenameText) {
                    filenameText.innerHTML = `File Terpilih: <strong>${fileName}</strong> (${sizeFormatted})`;
                    previewEl.style.display = 'inline-flex';
                }
            });
        }

        if (uploadForm) {
            uploadForm.addEventListener('submit', (e) => {
                hideError();
                if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                    e.preventDefault();
                    showError('<strong>Dokumen Belum Dipilih:</strong> Silakan pilih file dokumen PDF Laporan Akhir terlebih dahulu sebelum menekan tombol kirim.');
                    return;
                }

                const file = fileInput.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    e.preventDefault();
                    showError('<strong>Ukuran File Terlalu Besar:</strong> Ukuran file melebihi batas maksimal 5MB.');
                    return;
                }

                // Show loading state
                if (submitBtn) {
                    submitBtn.disabled = true;
                    if (btnText) btnText.textContent = 'Mengunggah Laporan...';
                }
            });
        }
    });

    // Admin Modal Script
    const verifModal = document.getElementById('verifikasi-laporan-modal');
    const verifForm = document.getElementById('verifikasi-laporan-form');

    function openVerifikasiLaporanModal(id, nama, status, catatan, url) {
        if (!verifModal) return;
        document.getElementById('modal-lap-nama').textContent = nama;
        document.getElementById('modal-lap-status').value = status === 'disetujui' ? 'disetujui' : 'revisi';
        document.getElementById('modal-lap-catatan').value = catatan || '';
        verifForm.action = url;

        verifModal.classList.add('active');
        verifModal.setAttribute('aria-hidden', 'false');
    }

    function closeVerifikasiLaporanModal() {
        if (!verifModal) return;
        verifModal.classList.remove('active');
        verifModal.setAttribute('aria-hidden', 'true');
    }

    if (verifModal) {
        verifModal.addEventListener('click', (e) => {
            if (e.target === verifModal) closeVerifikasiLaporanModal();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' || e.key === 'Esc') {
                if (verifModal.classList.contains('active')) closeVerifikasiLaporanModal();
            }
        });
    }
</script>
@endsection
