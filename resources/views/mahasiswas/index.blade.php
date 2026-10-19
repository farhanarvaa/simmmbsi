@extends('layouts.app')

@section('title', 'SIMMM - Pendaftar & Mahasiswa Magang')

@section('content')
<x-topbar title="Daftar Mahasiswa Magang" subtitle="Kelola pendaftaran, verifikasi berkas cabang, penempatan cabang/unit, dan pelaporan administratif RO Pusat." />

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

<div class="card">
    <div class="card-header">
        <div>
            <h3>Daftar Peserta Magang</h3>
            <div class="card-sub">Verifikasi berkas, keputusan penerimaan cabang, dan pencatatan administratif RO Pusat.</div>
        </div>
    </div>

    {{-- Filter Tabs: Semua vs Aktif vs Selesai (Arsip) --}}
    @php
        $totalAll = $mahasiswas->count();
        $totalAktif = $mahasiswas->where('status', '!=', 'selesai')->count();
        $totalSelesai = $mahasiswas->where('status', 'selesai')->count();
    @endphp
    <div style="display:flex; gap:8px; margin-bottom:18px; border-bottom:1px solid var(--line); padding-bottom:14px; flex-wrap:wrap;">
        <button type="button" class="btn btn-sm btn-tab btn-solid" onclick="filterMahasiswa('all', this)" id="tab-btn-all">
            Semua Pendaftar ({{ $totalAll }})
        </button>
        <button type="button" class="btn btn-sm btn-tab btn-ghost" onclick="filterMahasiswa('aktif', this)" id="tab-btn-aktif">
            Aktif / Berjalan ({{ $totalAktif }})
        </button>
        <button type="button" class="btn btn-sm btn-tab btn-ghost" onclick="filterMahasiswa('selesai', this)" id="tab-btn-selesai">
            Selesai / Lulus ({{ $totalSelesai }})
        </button>
    </div>

    @if($mahasiswas->isEmpty())
        <div class="empty-state">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3>Belum Ada Data Mahasiswa</h3>
            <p>Belum ada berkas pendaftaran magang yang masuk ke sistem.</p>
            <div style="margin-top:14px;">
                <a href="{{ route('mahasiswas.create') }}" class="btn btn-solid btn-sm">+ Tambah Mahasiswa Baru</a>
            </div>
        </div>
    @else
        <div class="table-responsive">
            <table id="mahasiswa-admin-table">
                <thead>
                    <tr>
                        <th>NIM / Nama</th>
                        <th>Universitas &amp; Jurusan</th>
                        <th>Dokumen Berkas</th>
                        <th>Keputusan Cabang</th>
                        <th>Lapor RO (Administratif)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mahasiswas as $mahasiswa)
                        @php 
                            $st = $mahasiswa->status_pendaftaran ?? 'diajukan';
                            $isMhsSelesai = $mahasiswa->status === 'selesai';
                        @endphp
                        <tr class="mhs-row" data-status="{{ $isMhsSelesai ? 'selesai' : 'aktif' }}">
                            <td>
                                <strong>{{ $mahasiswa->nama_lengkap }}</strong>
                                <div class="mono" style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $mahasiswa->nim }}</div>
                            </td>
                            <td>
                                <div>{{ $mahasiswa->universitas }}</div>
                                <div style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $mahasiswa->jurusan }}</div>
                            </td>
                            <td>
                                <div style="display:flex; flex-direction:column; gap:4px; font-size:12px;">
                                    @if($mahasiswa->proposal_magang)
                                        <a href="{{ asset('storage/' . $mahasiswa->proposal_magang) }}" target="_blank" style="color:var(--primary); font-weight:600; text-decoration:underline;">
                                            📄 Proposal Magang (PDF)
                                        </a>
                                    @else
                                        <span style="color:var(--muted);">Proposal: -</span>
                                    @endif

                                    @if($mahasiswa->surat_pengantar)
                                        <a href="{{ asset('storage/' . $mahasiswa->surat_pengantar) }}" target="_blank" style="color:var(--ink-soft); text-decoration:underline;">
                                            📑 Surat Pengantar
                                        </a>
                                    @endif

                                    @if($mahasiswa->cv)
                                        <a href="{{ asset('storage/' . $mahasiswa->cv) }}" target="_blank" style="color:var(--ink-soft); text-decoration:underline;">
                                            💼 CV
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if ($isMhsSelesai)
                                    <span class="badge badge-ok" style="background:#e6f4ea; color:#137333; font-weight:700;">✓ SELESAI / LULUS</span>
                                    <div style="font-size:11px; color:var(--muted); margin-top:2px;">Cabang/Unit Penempatan: {{ $mahasiswa->cabang?->nama ?: 'Belum ditentukan' }}</div>
                                @elseif ($st === 'diajukan')
                                    <span class="badge badge-wait">DIAJUKAN</span>
                                @elseif ($st === 'revisi')
                                    <span class="badge badge-danger">PERLU REVISI</span>
                                @elseif ($st === 'ditolak')
                                    <span class="badge badge-danger">DITOLAK</span>
                                @elseif ($st === 'diterima')
                                    <span class="badge badge-ok">DITERIMA CABANG</span>
                                @elseif ($st === 'ditempatkan')
                                    <span class="badge badge-ok">DITEMPATKAN ({{ $mahasiswa->cabang?->nama ?? 'Belum ditentukan' }})</span>
                                @endif
                            </td>
                            <td>
                                {{-- Pelaporan RO Pusat (Administratif) --}}
                                @if ($isMhsSelesai)
                                    <span class="badge badge-ok" title="Pelaporan RO selesai">RO SELESAI</span>
                                    @if($mahasiswa->nomor_referensi_laporan_ro)
                                        <div style="font-size:11px; color:var(--muted); margin-top:2px;">Ref: {{ $mahasiswa->nomor_referensi_laporan_ro }}</div>
                                    @endif
                                @elseif(in_array($st, ['diterima', 'ditempatkan']))
                                    @if($mahasiswa->status_pelaporan_ro === 'sudah_dilaporkan')
                                        <span class="badge badge-ok" title="No. Ref: {{ $mahasiswa->nomor_referensi_laporan_ro }}">RO DILAPORKAN</span>
                                        <div style="font-size:11px; color:var(--muted); margin-top:2px;">Ref: {{ $mahasiswa->nomor_referensi_laporan_ro }}</div>
                                    @else
                                        <button type="button" class="btn btn-ghost btn-sm" style="font-size:11.5px; padding:4px 8px; color:var(--amber);" onclick="openLaporRoModal({{ $mahasiswa->id }}, '{{ e($mahasiswa->nama_lengkap) }}', '{{ route('mahasiswas.lapor-ro', $mahasiswa->id) }}')">
                                            Lapor RO Pusat
                                        </button>
                                    @endif
                                @else
                                    <span style="font-size:12px; color:var(--muted);">—</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
                                    @if ($isMhsSelesai)
                                        <span class="badge" style="background:var(--line); color:var(--muted); font-size:11px; padding:4px 8px;">ARSIP SELESAI</span>
                                        <a href="{{ route('sertifikasi.index') }}" class="btn btn-ghost btn-sm" style="font-size:11.5px; padding:4px 8px;">Sertifikasi</a>
                                    @else
                                        {{-- Tombol Keputusan Cabang untuk mahasiswa aktif --}}
                                        @if($st === 'diajukan' || $st === 'revisi')
                                            <button type="button" class="btn btn-solid btn-sm" onclick="openKeputusanModal({{ $mahasiswa->id }}, '{{ e($mahasiswa->nama_lengkap) }}', '{{ route('mahasiswas.verifikasi', $mahasiswa->id) }}')">
                                                Keputusan Cabang
                                            </button>
                                        @elseif($st === 'diterima')
                                            <a href="{{ route('penempatan.index') }}" class="btn btn-solid btn-sm">Set Penempatan</a>
                                            <a href="{{ route('penempatan.index') }}" class="btn btn-ghost btn-sm" style="font-size:11.5px; padding:4px 8px;">Surat Penerimaan</a>
                                        @elseif($st === 'ditempatkan')
                                            <a href="{{ route('penempatan.index') }}" class="btn btn-ghost btn-sm" style="font-size:11.5px; padding:4px 8px;">Surat Penerimaan</a>
                                        @endif
                                    @endif

                                    <a href="{{ route('mahasiswas.edit', $mahasiswa->id) }}" class="btn btn-ghost btn-sm">Edit</a>

                                    <form action="{{ route('mahasiswas.destroy', $mahasiswa->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus data pendaftar mahasiswa ini?" data-title="Hapus Data Mahasiswa">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
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

{{-- Modal Keputusan Penerimaan Cabang --}}
<div id="keputusan-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:480px;" role="dialog" aria-modal="true">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div class="modal-title" style="margin:0;">Keputusan Pendaftaran Cabang</div>
            <button type="button" onclick="closeKeputusanModal()" style="background:none; border:none; cursor:pointer; color:var(--muted); font-size:20px;">&times;</button>
        </div>

        <div style="font-size:13.5px; color:var(--muted); margin-bottom:16px;">
            Mahasiswa: <strong id="keputusan-mhs-nama" style="color:var(--ink);">-</strong>
        </div>

        <form id="form-keputusan" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Hasil Keputusan Admin/Mentor</label>
                <select name="status_pendaftaran" id="keputusan-status-select" onchange="toggleFormKeputusanFields()" required style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:14px;">
                    <option value="diterima">Diterima (Lanjut Penempatan)</option>
                    <option value="revisi">Perlu Revisi Berkas</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>

            <div id="warning-ditolak" style="margin-bottom:14px; display:none; background:var(--danger-bg); color:var(--danger); border:1px solid #efc0b9; padding:10px 14px; border-radius:6px; font-size:12.5px; line-height:1.4;">
                <strong>Perhatian:</strong> Menolak berkas pendaftaran akan menggugurkan pendaftaran mahasiswa ini. Pastikan Anda telah meninjau seluruh berkas secara seksama sebelum menyimpan.
            </div>

            <div id="field-catatan-revisi" style="margin-bottom:14px; display:none;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Catatan Revisi untuk Mahasiswa</label>
                <textarea name="catatan_revisi" placeholder="Tuliskan kekurangan/revisi berkas yang perlu diperbaiki" style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:13.5px; min-height:80px;"></textarea>
            </div>

            <div id="field-penempatan-cabang" style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Cabang/Unit Penempatan (Opsional)</label>
                <select name="id_cabang" style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:14px;">
                    <option value="">-- Tetapkan Nanti di Menu Penempatan --</option>
                    @foreach(\App\Models\Cabang::orderBy('nama')->get() as $cabang)
                        <option value="{{ $cabang->id }}">{{ $cabang->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--line); padding-top:14px; margin-top:16px;">
                <button type="button" class="btn btn-ghost" onclick="closeKeputusanModal()">Batal</button>
                <button type="submit" class="btn btn-solid" id="btn-submit-keputusan">Simpan Keputusan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Pencatatan Lapor RO Pusat (Administratif Non-Blocking) --}}
<div id="lapor-ro-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:480px;" role="dialog" aria-modal="true">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div class="modal-title" style="margin:0;">Pencatatan Pelaporan RO Pusat</div>
            <button type="button" onclick="closeLaporRoModal()" style="background:none; border:none; cursor:pointer; color:var(--muted); font-size:20px;">&times;</button>
        </div>

        <div style="font-size:13px; color:var(--muted); margin-bottom:16px;">
            Catat referensi laporan administratif ke Regional Office (RO) Pusat untuk mahasiswa <strong id="ro-mhs-nama" style="color:var(--ink);">-</strong>.
        </div>

        <form id="form-lapor-ro" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Nomor Referensi / Surat Laporan RO</label>
                <input type="text" name="nomor_referensi_laporan_ro" placeholder="Contoh: BSI-YOG/RO-LAP/2026/08/042" required style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:14px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Tanggal Pelaporan</label>
                <input type="date" name="tanggal_pelaporan_ro" value="{{ now()->format('Y-m-d') }}" required style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:14px;">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--line); padding-top:14px;">
                <button type="button" class="btn btn-ghost" onclick="closeLaporRoModal()">Batal</button>
                <button type="submit" class="btn btn-solid" id="btn-submit-lapor-ro">Simpan Pelaporan RO</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openKeputusanModal(id, nama, url) {
        const form = document.getElementById('form-keputusan');
        if (form) form.dataset.confirmed = '';
        document.getElementById('keputusan-mhs-nama').textContent = nama;
        document.getElementById('form-keputusan').action = url;
        document.getElementById('keputusan-modal').classList.add('active');
        toggleFormKeputusanFields();
    }

    function closeKeputusanModal() {
        document.getElementById('keputusan-modal').classList.remove('active');
    }

    function toggleFormKeputusanFields() {
        const val = document.getElementById('keputusan-status-select').value;
        const revField = document.getElementById('field-catatan-revisi');
        const penField = document.getElementById('field-penempatan-cabang');
        const warnDitolak = document.getElementById('warning-ditolak');

        if (warnDitolak) warnDitolak.style.display = (val === 'ditolak') ? 'block' : 'none';

        if (val === 'revisi') {
            revField.style.display = 'block';
            penField.style.display = 'none';
        } else if (val === 'diterima') {
            revField.style.display = 'none';
            penField.style.display = 'block';
        } else {
            revField.style.display = 'none';
            penField.style.display = 'none';
        }
    }

    function openLaporRoModal(id, nama, url) {
        document.getElementById('ro-mhs-nama').textContent = nama;
        document.getElementById('form-lapor-ro').action = url;
        document.getElementById('lapor-ro-modal').classList.add('active');
    }

    function filterMahasiswa(type, btn) {
        document.querySelectorAll('.btn-tab').forEach(b => {
            b.className = 'btn btn-sm btn-tab btn-ghost';
        });
        if (btn) btn.className = 'btn btn-sm btn-tab btn-solid';

        const rows = document.querySelectorAll('.mhs-row');
        rows.forEach(r => {
            const status = r.getAttribute('data-status');
            if (type === 'all') {
                r.style.display = '';
            } else if (type === 'aktif') {
                r.style.display = (status === 'aktif') ? '' : 'none';
            } else if (type === 'selesai') {
                r.style.display = (status === 'selesai') ? '' : 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const kepModal = document.getElementById('keputusan-modal');
        const roModal = document.getElementById('lapor-ro-modal');
        const formKeputusan = document.getElementById('form-keputusan');
        const submitBtnKep = document.getElementById('btn-submit-keputusan');
        const formLaporRo = document.getElementById('form-lapor-ro');
        const submitBtnRo = document.getElementById('btn-submit-lapor-ro');

        if (formKeputusan) {
            formKeputusan.addEventListener('submit', function(e) {
                const statusVal = document.getElementById('keputusan-status-select').value;
                if (statusVal === 'ditolak' && !formKeputusan.dataset.confirmed) {
                    e.preventDefault();
                    if (typeof showConfirmModal === 'function') {
                        showConfirmModal({
                            title: 'Konfirmasi Tolak Pendaftaran',
                            message: 'Apakah Anda yakin ingin MENOLAK berkas pendaftaran mahasiswa ini? Aksi ini akan mengubah status berkas menjadi Ditolak dan mahasiswa tidak dapat melanjutkan ke tahap berikutnya.',
                            confirmText: 'Ya, Tolak Pendaftaran',
                            variant: 'danger',
                            onConfirm: () => {
                                formKeputusan.dataset.confirmed = 'true';
                                if (submitBtnKep) {
                                    submitBtnKep.disabled = true;
                                    submitBtnKep.textContent = 'Menyimpan...';
                                }
                                formKeputusan.submit();
                            }
                        });
                    } else if (confirm('Apakah Anda yakin ingin MENOLAK pendaftaran mahasiswa ini? Aksi ini tidak dapat dibatalkan.')) {
                        if (submitBtnKep) {
                            submitBtnKep.disabled = true;
                            submitBtnKep.textContent = 'Menyimpan...';
                        }
                        formKeputusan.submit();
                    }
                    return;
                }
                if (submitBtnKep) {
                    submitBtnKep.disabled = true;
                    submitBtnKep.textContent = 'Menyimpan...';
                }
            });
        }

        if (formLaporRo) {
            formLaporRo.addEventListener('submit', function() {
                if (submitBtnRo) {
                    submitBtnRo.disabled = true;
                    submitBtnRo.textContent = 'Menyimpan...';
                }
            });
        }

        if (kepModal) {
            kepModal.addEventListener('click', (e) => {
                if (e.target === kepModal) closeKeputusanModal();
            });
        }
        if (roModal) {
            roModal.addEventListener('click', (e) => {
                if (e.target === roModal) closeLaporRoModal();
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' || e.key === 'Esc') {
                if (kepModal && kepModal.classList.contains('active')) closeKeputusanModal();
                if (roModal && roModal.classList.contains('active')) closeLaporRoModal();
            }
        });
    });
</script>
@endsection
