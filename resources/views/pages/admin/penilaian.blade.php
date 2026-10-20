@extends('layouts.app')

@section('title', 'SIMMM - Penilaian Magang')

@section('content')

@if(auth()->user()->role === 'admin')
{{-- ============================================================ --}}
{{-- TAMPILAN ADMIN/MENTOR                                        --}}
{{-- ============================================================ --}}
<x-topbar title="Penilaian Mahasiswa Magang" subtitle="Lihat seluruh mahasiswa dari semua status dan berikan penilaian akhir bagi yang telah menyelesaikan magang." />

@if (session('success'))
    <div class="flash">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="flash" style="background:var(--danger-bg); color:var(--danger); border-color:#efc0b9;">{{ session('error') }}</div>
@endif

{{-- Rekap Statistik --}}
@php
    $totalMhs        = $mahasiswas->count();
    $totalBerjalan   = $mahasiswas->where('status_pendaftaran', 'ditempatkan')->where('status', 'aktif')->count();
    $totalSelesai    = $mahasiswas->where('status', 'selesai')->count();
    $totalDinilai    = $mahasiswas->filter(fn($m) => $m->penilaian)->count();
    $rataRata        = $mahasiswas->filter(fn($m) => $m->penilaian)->avg(fn($m) => $m->penilaian->nilai_akhir);
@endphp

<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
    <div class="card" style="padding:20px; margin:0;">
        <div style="font-size:11px; font-weight:700; letter-spacing:.08em; color:var(--muted); text-transform:uppercase; margin-bottom:8px;">Total Mahasiswa</div>
        <div style="font-size:28px; font-weight:800; color:var(--ink);">{{ $totalMhs }}</div>
        <div style="font-size:12px; color:var(--muted); margin-top:4px;">semua status</div>
    </div>
    <div class="card" style="padding:20px; margin:0;">
        <div style="font-size:11px; font-weight:700; letter-spacing:.08em; color:var(--muted); text-transform:uppercase; margin-bottom:8px;">Sedang Berjalan</div>
        <div style="font-size:28px; font-weight:800; color:var(--primary);">{{ $totalBerjalan }}</div>
        <div style="font-size:12px; color:var(--muted); margin-top:4px;">aktif magang</div>
    </div>
    <div class="card" style="padding:20px; margin:0;">
        <div style="font-size:11px; font-weight:700; letter-spacing:.08em; color:var(--muted); text-transform:uppercase; margin-bottom:8px;">Telah Selesai</div>
        <div style="font-size:28px; font-weight:800; color:var(--green,#16a34a);">{{ $totalSelesai }}</div>
        <div style="font-size:12px; color:var(--muted); margin-top:4px;">{{ $totalDinilai }} sudah dinilai</div>
    </div>
    <div class="card" style="padding:20px; margin:0;">
        <div style="font-size:11px; font-weight:700; letter-spacing:.08em; color:var(--muted); text-transform:uppercase; margin-bottom:8px;">Rata-rata Nilai</div>
        <div style="font-size:28px; font-weight:800; color:var(--amber,#b45309);">{{ $rataRata ? number_format($rataRata, 1) : '—' }}</div>
        <div style="font-size:12px; color:var(--muted); margin-top:4px;">dari mahasiswa dinilai</div>
    </div>
</div>

{{-- Tabel Daftar Mahasiswa --}}
<div class="card">
    <div class="card-header">
        <div>
            <h3>Daftar Mahasiswa — Semua Status</h3>
            <div class="card-sub">Klik "Beri Penilaian" pada mahasiswa yang telah selesai magang untuk memberikan nilai akhir.</div>
        </div>
    </div>

    @if($mahasiswas->isEmpty())
        <div class="empty-state">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h3>Belum Ada Data Mahasiswa</h3>
            <p>Belum ada mahasiswa yang terdaftar pada sistem.</p>
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Universitas &amp; Jurusan</th>
                        <th>Cabang/Unit &amp; Periode</th>
                        <th>Status</th>
                        <th>Nilai Akhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mahasiswas as $mhs)
                    @php
                        $st  = $mhs->status_pendaftaran ?? 'diajukan';
                        $sts = $mhs->status ?? 'aktif';
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $mhs->nama_lengkap }}</strong>
                            <div class="mono" style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $mhs->nim }}</div>
                        </td>
                        <td>
                            <div>{{ $mhs->universitas }}</div>
                            <div style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $mhs->jurusan }}</div>
                        </td>
                        <td>
                            <div style="font-size:13px;">{{ $mhs->cabang?->nama ?? '—' }}</div>
                            @if($mhs->tanggal_mulai)
                                <div style="font-size:11.5px; color:var(--muted); margin-top:2px;">
                                    {{ \Carbon\Carbon::parse($mhs->tanggal_mulai)->format('d M Y') }}
                                    @if($mhs->tanggal_selesai) – {{ \Carbon\Carbon::parse($mhs->tanggal_selesai)->format('d M Y') }} @endif
                                </div>
                            @else
                                <div style="font-size:11.5px; color:var(--muted);">Tanggal belum diset</div>
                            @endif
                        </td>
                        <td>
                            @if($st === 'diajukan')
                                <span class="badge badge-wait">DIAJUKAN</span>
                            @elseif($st === 'revisi')
                                <span class="badge badge-danger">REVISI</span>
                            @elseif($st === 'ditolak')
                                <span class="badge badge-danger">DITOLAK</span>
                            @elseif($st === 'diterima')
                                <span class="badge badge-wait">DITERIMA</span>
                            @elseif($st === 'ditempatkan')
                                @if($sts === 'selesai')
                                    <span class="badge badge-ok">SELESAI</span>
                                @else
                                    <span class="badge badge-ok">BERJALAN</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($mhs->penilaian)
                                <div style="text-align:center;">
                                    <div style="font-size:22px; font-weight:800; color:var(--primary);">{{ number_format($mhs->penilaian->nilai_akhir, 1) }}</div>
                                    <span class="badge badge-ok" style="font-size:11px;">Predikat {{ $mhs->penilaian->predikat }}</span>
                                    @if($mhs->penilaian->file_formulir_kampus)
                                        <div style="margin-top:6px;">
                                            <a href="{{ route('penilaian.formulir', $mhs->penilaian->id) }}" target="_blank" style="font-size:11px; color:var(--primary); text-decoration:none; display:inline-flex; align-items:center; gap:4px; font-weight:600;">
                                                <svg style="width:13px; height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Formulir Kampus
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span style="font-size:13px; color:var(--muted);">Belum dinilai</span>
                            @endif
                        </td>
                        <td>
                            @if($st === 'ditempatkan')
                                <button
                                    type="button"
                                    class="btn btn-solid btn-sm"
                                    onclick="openPenilaianModal(
                                        {{ $mhs->id }},
                                        '{{ e($mhs->nama_lengkap) }}',
                                        '{{ $mhs->nim }}',
                                        {{ $mhs->penilaian ? json_encode($mhs->penilaian) : 'null' }}
                                    )"
                                >
                                    {{ $mhs->penilaian ? '✏ Edit Nilai' : '+ Beri Penilaian' }}
                                </button>
                            @else
                                <span style="font-size:12px; color:var(--muted);">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($mahasiswas->hasPages())
            <div class="pagination-container">
                {{ $mahasiswas->links() }}
            </div>
        @endif
    @endif
</div>

{{-- Modal Penilaian --}}
<div id="penilaian-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:540px;" role="dialog" aria-modal="true">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <div class="modal-title" style="margin:0;">Penilaian Akhir Magang</div>
            <button type="button" onclick="closePenilaianModal()" style="background:none; border:none; cursor:pointer; color:var(--muted); font-size:20px;">&times;</button>
        </div>
        <div id="penilaian-mhs-info" style="font-size:13px; color:var(--muted); margin-bottom:18px;"></div>

        <form id="form-penilaian" action="{{ route('penilaian.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="mahasiswa_id" id="penilaian-mahasiswa-id">

            {{-- Input Nilai Akhir & Predikat Otomatis --}}
            <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:14px; margin-bottom:18px; align-items:start;">
                <div>
                    <label for="input-nilai-akhir" style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">
                        Nilai Akhir (0 - 100) <span style="color:var(--danger);">*</span>
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        name="nilai_akhir"
                        id="input-nilai-akhir"
                        required
                        placeholder="Contoh: 85.50"
                        oninput="updateNilaiDanPredikat()"
                        style="width:100%; border:1px solid var(--line); border-radius:6px; padding:10px 12px; font-size:15px; font-weight:600; font-family:'Inter',sans-serif;"
                    >
                    <div style="font-size:11.5px; color:var(--muted); margin-top:4px;">Skala nilai: 0 sampai 100</div>
                </div>

                {{-- Preview Predikat Otomatis --}}
                <div style="background:var(--primary-light,#eff6ff); border:1px solid var(--line); border-radius:6px; padding:10px 14px; text-align:center;">
                    <div style="font-size:11.5px; color:var(--muted); margin-bottom:4px; font-weight:600; text-transform:uppercase; letter-spacing:.05em;">Predikat</div>
                    <div style="display:flex; flex-direction:column; align-items:center; gap:2px;">
                        <span id="preview-predikat" class="badge badge-ok" style="font-size:16px; font-weight:800; padding:4px 14px;">—</span>
                        <span id="preview-predikat-label" style="font-size:11.5px; color:var(--muted); font-weight:500;">Masukkan nilai</span>
                    </div>
                </div>
            </div>

            {{-- Upload Formulir Penilaian dari Kampus --}}
            <div style="margin-bottom:18px;">
                <label for="input-file-formulir" style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">
                    Formulir Penilaian dari Kampus <span id="label-file-required" style="color:var(--danger);">*</span>
                </label>
                <input
                    type="file"
                    name="file_formulir_kampus"
                    id="input-file-formulir"
                    accept=".pdf,.jpg,.jpeg,.png"
                    style="width:100%; border:1px solid var(--line); border-radius:6px; padding:8px 10px; font-size:13px; background:#fff;"
                >
                <div id="file-help-text" style="font-size:11.5px; color:var(--muted); margin-top:4px;">
                    Unggah hasil scan/foto formulir penilaian resmi kampus yang sudah diisi BSI (PDF, JPG, PNG, maks. 5MB).
                </div>
                <div id="preview-file-existing" style="display:none; margin-top:8px; padding:8px 12px; background:var(--bg,#f8fafc); border:1px dashed var(--line); border-radius:6px; font-size:12px;">
                    <span style="color:var(--muted);">File saat ini:</span>
                    <a id="link-file-existing" href="#" target="_blank" style="color:var(--primary); font-weight:600; text-decoration:none; margin-left:4px;">
                        📄 Lihat Formulir Penilaian Kampus
                    </a>
                </div>
            </div>

            {{-- Catatan Mentor (Minimal 10 Karakter) --}}
            <div style="margin-bottom:18px;">
                <label for="penilaian-catatan" style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">
                    Catatan dari Mentor <span style="color:var(--danger);">*</span>
                    <span style="font-weight:400; color:var(--muted); font-size:11.5px;">(Wajib, minimal 10 karakter)</span>
                </label>
                <textarea
                    name="catatan_mentor"
                    id="penilaian-catatan"
                    required
                    minlength="10"
                    placeholder="Tuliskan evaluasi performa, apresiasi, atau saran pengembangan bagi mahasiswa selama magang (minimal 10 karakter)..."
                    style="width:100%; border:1px solid var(--line); border-radius:6px; padding:10px 12px; font-size:13.5px; min-height:85px; font-family:'Inter',sans-serif; resize:vertical; line-height:1.5;"
                ></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--line); padding-top:14px;">
                <button type="button" class="btn btn-ghost" onclick="closePenilaianModal()">Batal</button>
                <button type="submit" class="btn btn-solid">Simpan Penilaian</button>
            </div>
        </form>
    </div>
</div>

<script>
function hitungPredikat(nilai) {
    if (isNaN(nilai) || nilai === '' || nilai === null) {
        return { predikat: '—', label: 'Masukkan nilai', className: 'badge' };
    }
    const n = parseFloat(nilai);
    if (n >= 85) return { predikat: 'A', label: 'Sangat Baik', className: 'badge badge-ok' };
    if (n >= 70) return { predikat: 'B', label: 'Baik', className: 'badge badge-ok' };
    if (n >= 55) return { predikat: 'C', label: 'Cukup', className: 'badge badge-wait' };
    return { predikat: 'D', label: 'Kurang', className: 'badge badge-danger' };
}

function updateNilaiDanPredikat() {
    const input = document.getElementById('input-nilai-akhir');
    const predEl = document.getElementById('preview-predikat');
    const labelEl = document.getElementById('preview-predikat-label');

    const result = hitungPredikat(input.value);
    predEl.textContent = result.predikat;
    predEl.className = result.className;
    labelEl.textContent = result.label;
}

function openPenilaianModal(id, nama, nim, existing) {
    document.getElementById('penilaian-mahasiswa-id').value = id;
    document.getElementById('penilaian-mhs-info').innerHTML =
        '<strong style="color:var(--ink);">' + nama + '</strong> &nbsp;|&nbsp; <span class="mono">' + nim + '</span>';

    const inputNilai = document.getElementById('input-nilai-akhir');
    const inputCatatan = document.getElementById('penilaian-catatan');
    const inputFile = document.getElementById('input-file-formulir');
    const labelReq = document.getElementById('label-file-required');
    const helpText = document.getElementById('file-help-text');
    const previewFile = document.getElementById('preview-file-existing');
    const linkFile = document.getElementById('link-file-existing');

    inputFile.value = '';

    if (existing) {
        inputNilai.value = existing.nilai_akhir ?? '';
        inputCatatan.value = existing.catatan_mentor ?? '';

        if (existing.file_formulir_kampus) {
            inputFile.required = false;
            labelReq.style.display = 'none';
            helpText.textContent = 'Biarkan kosong jika tidak ingin mengubah formulir yang sudah ada (PDF, JPG, PNG, maks. 5MB).';
            linkFile.href = '/penilaian/' + existing.id + '/formulir';
            previewFile.style.display = 'block';
        } else {
            inputFile.required = true;
            labelReq.style.display = 'inline';
            helpText.textContent = 'Unggah scan/foto formulir penilaian resmi kampus (PDF, JPG, PNG, maks. 5MB).';
            previewFile.style.display = 'none';
        }
    } else {
        inputNilai.value = '';
        inputCatatan.value = '';
        inputFile.required = true;
        labelReq.style.display = 'inline';
        helpText.textContent = 'Unggah scan/foto formulir penilaian resmi kampus (PDF, JPG, PNG, maks. 5MB).';
        previewFile.style.display = 'none';
    }

    updateNilaiDanPredikat();
    document.getElementById('penilaian-modal').classList.add('active');
}

function closePenilaianModal() {
    document.getElementById('penilaian-modal').classList.remove('active');
}
</script>

@else
{{-- ============================================================ --}}
{{-- TAMPILAN MAHASISWA (read-only)                              --}}
{{-- ============================================================ --}}
<x-topbar title="Penilaian Magang Saya" subtitle="Lihat hasil evaluasi akhir dari Admin/Mentor BSI Cabang." />

@if (session('error'))
    <div class="flash" style="background:var(--danger-bg); color:var(--danger); border-color:#efc0b9;">{{ session('error') }}</div>
@endif

@if($mahasiswa->penilaian)
@php $p = $mahasiswa->penilaian; @endphp

{{-- Kartu Nilai Akhir & Berkas Formulir Kampus --}}
<div style="display:grid; grid-template-columns:1fr 1.4fr; gap:20px; margin-bottom:24px;">
    {{-- Card 1: Nilai Akhir & Predikat --}}
    <div class="card" style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:32px 24px; text-align:center; margin:0;">
        <div style="font-size:12px; font-weight:700; color:var(--muted); margin-bottom:8px; text-transform:uppercase; letter-spacing:.08em;">Nilai Akhir Magang</div>
        <div style="font-size:56px; font-weight:900; color:var(--primary); line-height:1; letter-spacing:-0.02em;">{{ number_format($p->nilai_akhir, 1) }}</div>
        <div style="margin-top:12px;">
            @php
                $predikatColor = match($p->predikat) {
                    'A' => 'var(--green,#16a34a)',
                    'B' => 'var(--primary)',
                    'C' => 'var(--amber,#b45309)',
                    default => 'var(--danger)',
                };
                $predikatLabel = match($p->predikat) {
                    'A' => 'Sangat Baik',
                    'B' => 'Baik',
                    'C' => 'Cukup',
                    default => 'Kurang',
                };
            @endphp
            <span style="display:inline-block; background:{{ $predikatColor }}18; color:{{ $predikatColor }}; font-weight:700; font-size:14px; padding:6px 18px; border-radius:20px; border:1px solid {{ $predikatColor }}35;">
                Predikat {{ $p->predikat }} — {{ $predikatLabel }}
            </span>
        </div>
        <div style="font-size:12px; color:var(--muted); margin-top:16px; border-top:1px solid var(--line); padding-top:12px; width:100%;">
            Dinilai oleh: <strong style="color:var(--ink);">{{ $p->admin->name ?? 'Admin / Mentor' }}</strong><br>
            <span style="color:var(--muted);">{{ $p->updated_at->format('d M Y, H:i') }} WIB</span>
        </div>
    </div>

    {{-- Card 2: Berkas Formulir Penilaian Kampus --}}
    <div class="card" style="display:flex; flex-direction:column; justify-content:space-between; padding:28px 24px; margin:0;">
        <div>
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                <div style="width:40px; height:40px; border-radius:8px; background:var(--primary-light,#eff6ff); color:var(--primary); display:flex; align-items:center; justify-content:center;">
                    <svg style="width:22px; height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h3 style="margin:0; font-size:17px; font-weight:700;">Formulir Penilaian dari Kampus</h3>
                    <div style="font-size:12.5px; color:var(--muted); margin-top:2px;">Format resmi evaluasi universitas</div>
                </div>
            </div>

            <p style="font-size:13.5px; color:var(--ink-soft); line-height:1.6; margin-bottom:18px;">
                BSI mengisi dan menandatangani formulir evaluasi resmi yang disediakan oleh universitas/fakultas Anda. Silakan lihat atau unduh berkas scan formulir resmi di bawah ini.
            </p>
        </div>

        <div>
            @if($p->file_formulir_kampus)
                <a href="{{ route('penilaian.formulir', $p->id) }}" target="_blank" class="btn btn-solid" style="display:inline-flex; align-items:center; gap:8px; padding:10px 18px; font-size:13.5px; text-decoration:none;">
                    <svg style="width:18px; height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Lihat/Unduh Formulir Penilaian dari Kampus
                </a>
                <div style="font-size:11.5px; color:var(--muted); margin-top:8px;">
                    Format berkas: {{ strtoupper(pathinfo($p->file_formulir_kampus, PATHINFO_EXTENSION)) }} (Tersedia untuk dilihat dan diunduh)
                </div>
            @else
                <div style="padding:12px; background:var(--bg,#f8fafc); border:1px solid var(--line); border-radius:6px; color:var(--muted); font-size:13px;">
                    Formulir fisik kampus belum diunggah oleh mentor.
                </div>
            @endif
        </div>
    </div>
</div>

@if($p->catatan_mentor)
<div class="card">
    <div class="card-header">
        <div>
            <h3>Catatan dari Mentor</h3>
            <div class="card-sub">Masukan dan evaluasi dari Admin/Mentor BSI Cabang.</div>
        </div>
    </div>
    <div style="font-size:14px; color:var(--ink-soft); line-height:1.7; white-space:pre-wrap; background:var(--bg,#f8fafc); border:1px solid var(--line); border-radius:6px; padding:16px;">{{ $p->catatan_mentor }}</div>
</div>
@endif

@else
<div class="card">
    <div class="empty-state">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3>Penilaian Belum Tersedia</h3>
        <p>Penilaian akhir akan diberikan oleh Admin/Mentor setelah masa magang selesai. Terus semangat!</p>
    </div>
</div>
@endif

@endif
@endsection
