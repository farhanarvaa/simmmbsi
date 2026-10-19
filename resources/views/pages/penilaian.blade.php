@extends('layouts.app')

@section('title', 'SIMMM - Penilaian Magang')

@section('content')

@if(auth()->user()->role === 'admin')
{{-- ============================================================ --}}
{{-- TAMPILAN ADMIN/MENTOR                                        --}}
{{-- ============================================================ --}}
<div class="topbar">
    <div>
        <h1>Penilaian Mahasiswa Magang</h1>
        <div class="subtitle">Lihat seluruh mahasiswa dari semua status dan berikan penilaian akhir bagi yang telah menyelesaikan magang.</div>
    </div>
</div>

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
            <div style="margin-top:20px; display:flex; justify-content:center;">
                {{ $mahasiswas->links() }}
            </div>
        @endif
    @endif
</div>

{{-- Modal Penilaian --}}
<div id="penilaian-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:520px;" role="dialog" aria-modal="true">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <div class="modal-title" style="margin:0;">Penilaian Akhir Magang</div>
            <button type="button" onclick="closePenilaianModal()" style="background:none; border:none; cursor:pointer; color:var(--muted); font-size:20px;">&times;</button>
        </div>
        <div id="penilaian-mhs-info" style="font-size:13px; color:var(--muted); margin-bottom:18px;"></div>

        <form id="form-penilaian" action="{{ route('penilaian.store') }}" method="POST">
            @csrf
            <input type="hidden" name="mahasiswa_id" id="penilaian-mahasiswa-id">

            {{-- 5 Aspek Penilaian --}}
            @php
                $aspeks = [
                    'nilai_kedisiplinan'   => 'Kedisiplinan',
                    'nilai_kerjasama'      => 'Kerjasama Tim',
                    'nilai_inisiatif'      => 'Inisiatif & Kreativitas',
                    'nilai_kualitas_kerja' => 'Kualitas Kerja',
                    'nilai_komunikasi'     => 'Komunikasi',
                ];
            @endphp

            <div style="display:flex; flex-direction:column; gap:14px; margin-bottom:18px;">
                @foreach($aspeks as $field => $label)
                <div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                        <label style="font-size:13px; font-weight:600;">{{ $label }}</label>
                        <span id="display-{{ $field }}" style="font-size:14px; font-weight:700; color:var(--primary); min-width:36px; text-align:right;">80</span>
                    </div>
                    <input
                        type="range"
                        name="{{ $field }}"
                        id="input-{{ $field }}"
                        min="0" max="100" value="80"
                        oninput="document.getElementById('display-{{ $field }}').textContent = this.value; updateNilaiAkhir();"
                        style="width:100%; accent-color:var(--primary); height:6px; cursor:pointer;"
                    >
                    <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--muted); margin-top:2px;">
                        <span>0 (Sangat Kurang)</span><span>100 (Sangat Baik)</span>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Preview Nilai Akhir --}}
            <div style="background:var(--primary-light,#eff6ff); border:1px solid var(--line); border-radius:8px; padding:14px; margin-bottom:14px; display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <div style="font-size:12px; color:var(--muted); margin-bottom:2px;">Nilai Akhir (Rata-rata)</div>
                    <div id="preview-nilai-akhir" style="font-size:26px; font-weight:800; color:var(--primary);">80.00</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:12px; color:var(--muted); margin-bottom:4px;">Predikat</div>
                    <span id="preview-predikat" class="badge badge-ok" style="font-size:15px; padding:4px 12px;">B</span>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px;">Catatan Mentor <span style="font-weight:400; color:var(--muted);">(Opsional)</span></label>
                <textarea name="catatan_mentor" id="penilaian-catatan" placeholder="Tambahkan catatan evaluasi, saran, atau komentar untuk mahasiswa ini..." style="width:100%; border:1px solid var(--line); border-radius:4px; padding:10px 12px; font-size:13.5px; min-height:80px; font-family:'Inter',sans-serif; resize:vertical;"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--line); padding-top:14px;">
                <button type="button" class="btn btn-ghost" onclick="closePenilaianModal()">Batal</button>
                <button type="submit" class="btn btn-solid">Simpan Penilaian</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPenilaianModal(id, nama, nim, existing) {
    document.getElementById('penilaian-mahasiswa-id').value = id;
    document.getElementById('penilaian-mhs-info').innerHTML =
        '<strong style="color:var(--ink);">' + nama + '</strong> &nbsp;|&nbsp; <span class="mono">' + nim + '</span>';

    const aspeks = ['nilai_kedisiplinan','nilai_kerjasama','nilai_inisiatif','nilai_kualitas_kerja','nilai_komunikasi'];

    if (existing) {
        aspeks.forEach(function(k) {
            const val = existing[k] ?? 80;
            document.getElementById('input-' + k).value = val;
            document.getElementById('display-' + k).textContent = val;
        });
        document.getElementById('penilaian-catatan').value = existing.catatan_mentor ?? '';
    } else {
        aspeks.forEach(function(k) {
            document.getElementById('input-' + k).value = 80;
            document.getElementById('display-' + k).textContent = 80;
        });
        document.getElementById('penilaian-catatan').value = '';
    }

    updateNilaiAkhir();
    document.getElementById('penilaian-modal').classList.add('active');
}

function closePenilaianModal() {
    document.getElementById('penilaian-modal').classList.remove('active');
}

function updateNilaiAkhir() {
    const aspeks = ['nilai_kedisiplinan','nilai_kerjasama','nilai_inisiatif','nilai_kualitas_kerja','nilai_komunikasi'];
    let total = 0;
    aspeks.forEach(function(k) {
        total += parseInt(document.getElementById('input-' + k).value || 0);
    });
    const avg = total / aspeks.length;
    document.getElementById('preview-nilai-akhir').textContent = avg.toFixed(2);

    let predikat = 'D';
    if (avg >= 85) predikat = 'A';
    else if (avg >= 70) predikat = 'B';
    else if (avg >= 55) predikat = 'C';

    const predEl = document.getElementById('preview-predikat');
    predEl.textContent = predikat;
    predEl.className = 'badge ' + (predikat === 'A' || predikat === 'B' ? 'badge-ok' : 'badge-wait');
    if (predikat === 'D') predEl.className = 'badge badge-danger';
}
</script>

@else
{{-- ============================================================ --}}
{{-- TAMPILAN MAHASISWA (read-only)                              --}}
{{-- ============================================================ --}}
<div class="topbar">
    <div>
        <h1>Penilaian Magang Saya</h1>
        <div class="subtitle">Lihat hasil evaluasi akhir dari Admin/Mentor BSI Cabang.</div>
    </div>
</div>

@if (session('error'))
    <div class="flash" style="background:var(--danger-bg); color:var(--danger); border-color:#efc0b9;">{{ session('error') }}</div>
@endif

@if($mahasiswa->penilaian)
@php $p = $mahasiswa->penilaian; @endphp

{{-- Kartu Nilai Akhir --}}
<div style="display:grid; grid-template-columns:1fr 2fr; gap:20px; margin-bottom:24px;">
    <div class="card" style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:32px 20px; text-align:center;">
        <div style="font-size:13px; font-weight:600; color:var(--muted); margin-bottom:8px; text-transform:uppercase; letter-spacing:.06em;">Nilai Akhir</div>
        <div style="font-size:56px; font-weight:900; color:var(--primary); line-height:1;">{{ number_format($p->nilai_akhir, 1) }}</div>
        <div style="margin-top:10px;">
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
            <span style="display:inline-block; background:{{ $predikatColor }}20; color:{{ $predikatColor }}; font-weight:700; font-size:14px; padding:4px 16px; border-radius:20px; border:1px solid {{ $predikatColor }}40;">
                Predikat {{ $p->predikat }} — {{ $predikatLabel }}
            </span>
        </div>
        <div style="font-size:12px; color:var(--muted); margin-top:12px;">
            Dinilai oleh: <strong>{{ $p->admin->name ?? 'Admin' }}</strong><br>
            {{ $p->updated_at->format('d M Y') }}
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h3>Detail Per Aspek Penilaian</h3>
                <div class="card-sub">Rincian nilai dari setiap aspek evaluasi.</div>
            </div>
        </div>
        @php
            $aspeksLabel = [
                'nilai_kedisiplinan'   => 'Kedisiplinan',
                'nilai_kerjasama'      => 'Kerjasama Tim',
                'nilai_inisiatif'      => 'Inisiatif & Kreativitas',
                'nilai_kualitas_kerja' => 'Kualitas Kerja',
                'nilai_komunikasi'     => 'Komunikasi',
            ];
        @endphp
        <div style="display:flex; flex-direction:column; gap:14px;">
            @foreach($aspeksLabel as $field => $label)
            @php $val = $p->$field; @endphp
            <div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
                    <span style="font-size:13px; font-weight:600;">{{ $label }}</span>
                    <span style="font-size:14px; font-weight:700; color:var(--primary);">{{ $val }}<span style="font-size:11px; font-weight:400; color:var(--muted);">/100</span></span>
                </div>
                <div style="height:8px; background:var(--line); border-radius:99px; overflow:hidden;">
                    <div style="height:100%; width:{{ $val }}%; background:var(--primary); border-radius:99px; transition:width .5s;"></div>
                </div>
            </div>
            @endforeach
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
    <div style="font-size:14px; color:var(--ink-soft); line-height:1.7; white-space:pre-wrap;">{{ $p->catatan_mentor }}</div>
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
