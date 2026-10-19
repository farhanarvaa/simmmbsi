@extends('layouts.app')

@section('title', 'SIMMM - Surat Keterangan Selesai Magang')

@section('content')
<x-topbar title="Surat Keterangan Selesai Magang" :subtitle="$isAdmin ? 'Daftar dan penerbitan surat keterangan bagi mahasiswa yang telah menyelesaikan program magang.' : 'Unduh dan cetak surat keterangan selesai magang dari Bank Syariah Indonesia Area Yogyakarta.'" />

@if (session('success') || session('error'))
    <div class="flash" style="{{ session('error') ? 'background:var(--danger-bg); color:var(--danger); border-color:#efc0b9;' : '' }}">
        <div style="display:flex; align-items:center; gap:10px;">
            @if(session('error'))
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            @else
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            @endif
            <span>{{ session('error') ?? session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
    </div>
@endif

@if(!$isAdmin)
    {{-- TAMPILAN MAHASISWA --}}
    @php
        $isSelesai = $mahasiswa && ($mahasiswa->status === 'selesai' || $mahasiswa->penilaian);
        $penilaian = $mahasiswa->penilaian ?? null;
        $surat = $mahasiswa->suratKeterangan ?? null;
        $noSertifikat = $surat?->nomor_surat ?? 'Belum diterbitkan';
        $tglTerbit = $surat?->issued_at?->isoFormat('D MMMM Y') ?? '-';
    @endphp

    @if($isSelesai)
        {{-- Card Sertifikat Resmi Mahasiswa --}}
        <div class="card" style="border: 2px solid var(--accent); background: linear-gradient(180deg, #FFFFFF 0%, #FAF8F2 100%); position:relative; overflow:hidden;">
            <div style="position:absolute; right:-20px; top:-20px; width:140px; height:140px; background:radial-gradient(circle, rgba(200,155,60,0.15) 0%, rgba(200,155,60,0) 70%); border-radius:50%; pointer-events:none;"></div>

            <div class="card-header" style="border-bottom:1px dashed var(--accent);">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:42px; height:42px; border-radius:10px; background:var(--accent-soft); display:flex; align-items:center; justify-content:center; color:var(--amber);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                    </div>
                    <div>
                        <h3 style="color:var(--primary-dark); font-size:19px;">Surat Keterangan Selesai Magang</h3>
                        <div class="card-sub">Bank Syariah Indonesia Area Yogyakarta</div>
                    </div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:20px; padding:20px; background:var(--surface); border:1px solid var(--line); border-radius:10px; margin-bottom:24px;">
                <div>
                    <div style="font-size:11.5px; color:var(--muted); text-transform:uppercase; font-weight:700; letter-spacing:0.06em; margin-bottom:4px;">Nomor Surat</div>
                    <div class="mono" style="font-size:14px; font-weight:700; color:var(--primary-dark);">{{ $noSertifikat }}</div>
                </div>
                <div>
                    <div style="font-size:11.5px; color:var(--muted); text-transform:uppercase; font-weight:700; letter-spacing:0.06em; margin-bottom:4px;">Nama Mahasiswa</div>
                    <div style="font-size:14.5px; font-weight:700; color:var(--ink);">{{ $mahasiswa->nama_lengkap }}</div>
                    <div style="font-size:12px; color:var(--muted);">NIM: {{ $mahasiswa->nim }}</div>
                </div>
                <div>
                    <div style="font-size:11.5px; color:var(--muted); text-transform:uppercase; font-weight:700; letter-spacing:0.06em; margin-bottom:4px;">Cabang/Unit Penempatan</div>
                    <div style="font-size:14px; font-weight:600; color:var(--ink);">{{ $mahasiswa->cabang?->nama ?: 'Belum ditentukan' }}</div>
                    <div style="font-size:12px; color:var(--muted);">{{ $mahasiswa->universitas }}</div>
                </div>
                <div>
                    <div style="font-size:11.5px; color:var(--muted); text-transform:uppercase; font-weight:700; letter-spacing:0.06em; margin-bottom:4px;">Tanggal Terbit</div>
                    <div style="font-size:14px; font-weight:600; color:var(--ink);">{{ $tglTerbit }}</div>
                </div>
            </div>

           

            <div style="display:flex; justify-content:flex-end; gap:12px; align-items:center;">
                @if($surat)
                    <button type="button" class="btn btn-ghost" onclick="openCertificateModal('{{ $surat->nomor_surat }}', '{{ e($mahasiswa->nama_lengkap) }}', '{{ $mahasiswa->nim }}', '{{ e($mahasiswa->universitas) }}', '{{ $mahasiswa->cabang?->nama ?: 'Belum ditentukan' }}', '{{ $penilaian->predikat ?? '-' }}', '{{ $penilaian ? number_format($penilaian->nilai_akhir,1) : '-' }}', '{{ $tglTerbit }}', '{{ route('sertifikasi.preview', $mahasiswa) }}')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        Lihat Surat
                    </button>
                    <a href="{{ route('sertifikasi.download', $mahasiswa) }}" class="btn btn-solid">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
                        Cetak / Unduh PDF
                    </a>
                @else
                    <span class="btn btn-ghost" style="opacity:.65; cursor:not-allowed;">Menunggu Surat Diterbitkan</span>
                @endif
            </div>
        </div>
    @else
        <div class="card">
            <div class="empty-state">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                <h3>Surat Keterangan Belum Diterbitkan</h3>
                <p>Surat Keterangan Selesai Magang akan otomatis diterbitkan setelah evaluasi penilaian akhir selesai diberikan oleh Admin/Mentor dan status magang Anda dinyatakan <strong>Selesai</strong>.</p>
            </div>
        </div>
    @endif

@else
    {{-- TAMPILAN ADMIN / MENTOR --}}
    <div class="card">
        <div class="card-header">
            <div>
                <h3>Daftar Surat Keterangan Selesai Magang</h3>
                <div class="card-sub">Rekapitulasi surat keterangan mahasiswa magang yang telah menyelesaikan program.</div>
            </div>
        </div>

        @if($mahasiswas->isEmpty())
            <div class="empty-state">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                <h3>Belum Ada Surat Keterangan yang Diterbitkan</h3>
                <p>Surat keterangan akan otomatis diterbitkan setelah mahasiswa menyelesaikan masa magang dan diberikan evaluasi penilaian akhir.</p>
                <div style="margin-top:14px;">
                    <a href="{{ route('penilaian.index') }}" class="btn btn-ghost btn-sm" style="border:1px solid var(--line);">Buka Menu Penilaian Mahasiswa</a>
                </div>
            </div>
        @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nomor Surat</th>
                        <th>Mahasiswa &amp; NIM</th>
                        <th>Perguruan Tinggi &amp; Cabang/Unit</th>
                        <th>Nilai &amp; Predikat</th>
                        <th>Tanggal Terbit</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mahasiswas as $mhs)
                        @php
                            $pen = $mhs->penilaian;
                            $surat = $mhs->suratKeterangan;
                            $canIssue = $pen
                                && collect(['nilai_kedisiplinan', 'nilai_kerjasama', 'nilai_inisiatif', 'nilai_kualitas_kerja', 'nilai_komunikasi'])->every(fn ($field) => $pen->{$field} !== null)
                                && filled($mhs->nama_lengkap) && filled($mhs->nim) && filled($mhs->jurusan)
                                && filled($mhs->fakultas) && filled($mhs->universitas)
                                && filled($mhs->tanggal_mulai) && filled($mhs->tanggal_selesai);
                            $noCert = $surat?->nomor_surat ?? '-';
                            $tgl = $surat?->issued_at?->isoFormat('D MMMM Y') ?? '-';
                        @endphp
                        <tr>
                            <td><strong class="mono" style="color:var(--primary); font-size:13px;">{{ $noCert }}</strong></td>
                            <td>
                                <strong>{{ $mhs->nama_lengkap }}</strong>
                                <div style="font-size:12px; color:var(--muted);">NIM: {{ $mhs->nim }}</div>
                            </td>
                            <td>
                                <div>{{ $mhs->universitas }}</div>
                                <div style="font-size:12px; color:var(--primary); font-weight:600;">Cabang/Unit Penempatan: {{ $mhs->cabang?->nama ?: 'Belum ditentukan' }}</div>
                            </td>
                            <td>
                                @if($pen)
                                    <div><strong>{{ number_format($pen->nilai_akhir, 1) }}</strong> <span class="badge badge-ok">Predikat {{ $pen->predikat }}</span></div>
                                @else
                                    <span style="font-size:12px; color:var(--muted);">Belum dinilai</span>
                                @endif
                            </td>
                            <td>{{ $tgl }}</td>
                            <td>
                                @if($surat)
                                    <button type="button" class="btn btn-ghost btn-sm" onclick="openCertificateModal('{{ $surat->nomor_surat }}', '{{ e($mhs->nama_lengkap) }}', '{{ $mhs->nim }}', '{{ e($mhs->universitas) }}', '{{ $mhs->cabang?->nama ?: 'Belum ditentukan' }}', '{{ $pen->predikat ?? '-' }}', '{{ $pen ? number_format($pen->nilai_akhir,1) : '-' }}', '{{ $tgl }}', '{{ route('sertifikasi.preview', $mhs) }}')">
                                        Lihat Surat
                                    </button>
                                    @if($isAdmin)
                                        <form method="POST" action="{{ route('sertifikasi.issue', $mhs) }}" style="display:inline-flex;">
                                            @csrf
                                            <button type="submit" class="btn btn-ghost btn-sm">Perbarui PDF</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('sertifikasi.download', $mhs) }}" class="btn btn-solid btn-sm">Cetak / Unduh PDF</a>
                                @elseif($canIssue)
                                    <form method="POST" action="{{ route('sertifikasi.issue', $mhs) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-solid btn-sm">Terbitkan Sertifikat</button>
                                    </form>
                                @else
                                    <span style="font-size:12px; color:var(--muted);">Nilai belum lengkap</span>
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
@endif

{{-- Modal Preview & Cetak Sertifikat --}}
<div id="certificate-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box cert-modal-dialog" role="dialog" aria-modal="true">
        <div class="cert-modal-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--line); padding-bottom:12px;">
            <div class="modal-title" style="margin:0; font-size:17px; font-weight:700;">Pratinjau Surat Keterangan Selesai Magang</div>
            <button type="button" onclick="closeCertificateModal()" aria-label="Tutup modal" style="background:none; border:none; cursor:pointer; color:var(--muted); font-size:24px; line-height:1; padding:0 4px;">&times;</button>
        </div>

        <iframe id="certificate-preview-frame" title="Pratinjau Surat Keterangan Selesai Magang" style="display:block; width:100%; height:620px; border:1px solid #d0d0d0; background:#fff; border-radius:4px;"></iframe>

        {{-- Desain Surat Resmi BSI A4 Portrait --}}
        <div id="printable-certificate" class="cert-container" style="display:none;">

            {{-- KOP SURAT --}}
            <div class="surat-kop">
                <div class="surat-kop-logo">
                    <img src="{{ asset('images/logo_bsi.png') }}" alt="Logo BSI" class="cert-logo">
                </div>
                <div class="surat-kop-text">
                    <div class="surat-kop-title">PT BANK SYARIAH INDONESIA TBK</div>
                    <div class="surat-kop-sub">Area Yogyakarta</div>
                    <div class="surat-kop-address">Jl. Jend. Sudirman No.57, Yogyakarta 55223 &bull; Telp. (0274) 123456</div>
                </div>
            </div>
            <div class="surat-kop-line"></div>

            {{-- JUDUL SURAT --}}
            <div class="surat-judul-wrap">
                <div class="surat-judul">SURAT KETERANGAN SELESAI MAGANG</div>
                <div class="surat-nomor">Nomor: <span id="cert-modal-no">BSI-YOG/SKTM/2026/0001</span></div>
            </div>

            {{-- PEMBUKA --}}
            <div class="surat-body">
                <p class="surat-p">Yang bertanda tangan di bawah ini, Branch Manager PT Bank Syariah Indonesia Tbk Area Yogyakarta, dengan ini menerangkan bahwa :</p>

                {{-- DATA MAHASISWA --}}
                <table class="surat-data-table">
                    <tr>
                        <td class="sdt-label">Nama Lengkap</td>
                        <td class="sdt-sep">:</td>
                        <td class="sdt-value" id="cert-modal-nama">-</td>
                    </tr>
                    <tr>
                        <td class="sdt-label">NIM</td>
                        <td class="sdt-sep">:</td>
                        <td class="sdt-value" id="cert-modal-nim">-</td>
                    </tr>
                    <tr>
                        <td class="sdt-label">Perguruan Tinggi</td>
                        <td class="sdt-sep">:</td>
                        <td class="sdt-value" id="cert-modal-univ">-</td>
                    </tr>
                    <tr>
                        <td class="sdt-label">Program / Cabang/Unit</td>
                        <td class="sdt-sep">:</td>
                        <td class="sdt-value">Praktik Kerja Lapangan (PKL) / Magang &mdash; Cabang/Unit <span id="cert-modal-cabang">-</span></td>
                    </tr>
                    <tr>
                        <td class="sdt-label">Tempat Magang</td>
                        <td class="sdt-sep">:</td>
                        <td class="sdt-value">PT Bank Syariah Indonesia Tbk Area Yogyakarta</td>
                    </tr>
                    <tr>
                        <td class="sdt-label">Nilai Akhir</td>
                        <td class="sdt-sep">:</td>
                        <td class="sdt-value"><span id="cert-modal-nilai">-</span> &mdash; Predikat <strong id="cert-modal-predikat">-</strong></td>
                    </tr>
                </table>

                <p class="surat-p">Telah <strong>menyelesaikan</strong> Program Praktik Kerja Lapangan (PKL) / Magang di PT Bank Syariah Indonesia Tbk Area Yogyakarta dengan baik dan penuh tanggung jawab sesuai dengan ketentuan yang berlaku.</p>

                <p class="surat-p">Surat keterangan ini diberikan sebagai bukti bahwa yang bersangkutan telah benar-benar menyelesaikan program magang di instansi kami dan dapat dipergunakan sebagaimana mestinya.</p>
            </div>

            {{-- TANDA TANGAN --}}
            <div class="surat-ttd-wrap">
                <div class="surat-ttd-kota">
                    Yogyakarta, <span id="cert-modal-tgl">-</span>
                </div>
                <div class="surat-ttd-jabatan">PT Bank Syariah Indonesia Tbk</div>
                <div class="surat-ttd-jabatan">Area Yogyakarta</div>
                <div class="surat-ttd-space"></div>
                <div class="surat-ttd-nama">Branch Manager</div>
                <div class="surat-ttd-under">Pembimbing Magang BSI Area Yogyakarta</div>
            </div>

        </div>

        <div class="modal-actions cert-modal-footer" style="display:flex; justify-content:flex-end; gap:12px; margin-top:18px;">
            <button type="button" class="btn btn-ghost" onclick="closeCertificateModal()">Tutup</button>
            <button type="button" class="btn btn-solid" onclick="window.open(document.getElementById('certificate-preview-frame').src, '_blank')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Cetak / Unduh PDF
            </button>
        </div>
    </div>
</div>

<style>
    /* ===== MODAL DIALOG ===== */
    .cert-modal-dialog {
        max-width: 760px;
        width: 95%;
        padding: 24px;
        max-height: 92vh;
        overflow-y: auto;
    }

    /* ===== SURAT CONTAINER ===== */
    .cert-container {
        background: #ffffff;
        border: 1px solid #d0d0d0;
        padding: 40px 48px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border-radius: 4px;
        color: #1a1a1a;
        font-family: 'Times New Roman', Times, serif;
        font-size: 13px;
        line-height: 1.8;
    }

    /* ===== KOP SURAT ===== */
    .surat-kop {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 12px;
    }

    .surat-kop-logo {
        flex-shrink: 0;
    }

    .cert-logo {
        height: 52px;
        object-fit: contain;
    }

    .surat-kop-text {
        flex: 1;
    }

    .surat-kop-title {
        font-size: 15px;
        font-weight: 800;
        letter-spacing: 0.04em;
        color: #0A3F37;
        font-family: 'Arial', sans-serif;
        text-transform: uppercase;
    }

    .surat-kop-sub {
        font-size: 13px;
        font-weight: 600;
        color: #0A3F37;
        font-family: 'Arial', sans-serif;
    }

    .surat-kop-address {
        font-size: 11px;
        color: #555;
        font-family: 'Arial', sans-serif;
        margin-top: 2px;
    }

    .surat-kop-line {
        border: none;
        border-top: 4px solid #0A3F37;
        border-bottom: 1.5px solid #0A3F37;
        height: 0;
        margin: 6px 0 20px 0;
        padding: 2px 0 0 0;
    }

    /* ===== JUDUL SURAT ===== */
    .surat-judul-wrap {
        text-align: center;
        margin-bottom: 22px;
    }

    .surat-judul {
        font-size: 15px;
        font-weight: 700;
        text-transform: uppercase;
        text-decoration: underline;
        letter-spacing: 0.05em;
        color: #1a1a1a;
        font-family: 'Times New Roman', Times, serif;
    }

    .surat-nomor {
        font-size: 12.5px;
        color: #444;
        margin-top: 4px;
        font-family: 'Times New Roman', Times, serif;
    }

    /* ===== BODY SURAT ===== */
    .surat-body {
        margin-bottom: 24px;
    }

    .surat-p {
        text-align: justify;
        text-indent: 40px;
        margin-bottom: 10px;
        font-size: 13px;
        color: #1a1a1a;
    }

    /* ===== TABEL DATA ===== */
    .surat-data-table {
        width: 100%;
        border-collapse: collapse;
        margin: 14px 0 18px 40px;
        font-size: 13px;
    }

    .surat-data-table tr td {
        padding: 2px 0;
        vertical-align: top;
        color: #1a1a1a;
    }

    .sdt-label {
        width: 150px;
        font-weight: normal;
    }

    .sdt-sep {
        width: 16px;
        text-align: center;
        padding: 0 4px;
    }

    .sdt-value {
        font-weight: normal;
    }

    /* ===== TANDA TANGAN ===== */
    .surat-ttd-wrap {
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        text-align: center;
        min-width: 200px;
    }

    .surat-ttd-kota {
        font-size: 13px;
        color: #1a1a1a;
        margin-bottom: 4px;
        text-align: right;
        width: 100%;
        padding-right: 20px;
    }

    .surat-ttd-jabatan {
        font-size: 13px;
        font-weight: 600;
        color: #1a1a1a;
        text-align: right;
        width: 100%;
        padding-right: 20px;
    }

    .surat-ttd-space {
        height: 72px;
    }

    .surat-ttd-nama {
        font-size: 13px;
        font-weight: 700;
        text-decoration: underline;
        color: #1a1a1a;
        text-align: right;
        width: 100%;
        padding-right: 20px;
    }

    .surat-ttd-under {
        font-size: 12px;
        color: #444;
        text-align: right;
        width: 100%;
        padding-right: 20px;
    }

    /* ===== PRINT — A4 PORTRAIT ===== */
    @page {
        size: A4 portrait;
        margin: 20mm 20mm 20mm 25mm;
    }

    @media print {
        *, *:before, *:after {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box !important;
        }

        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #FFFFFF !important;
        }

        /* Sembunyikan semua elemen UI */
        .sidebar, .sidebar-overlay, .app-header, .topbar, .card,
        #flash-container, .cert-modal-header, .cert-modal-footer,
        #confirm-dialog-overlay {
            display: none !important;
        }

        .app-layout, .main-content {
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
        }

        .modal-overlay {
            position: static !important;
            background: #FFFFFF !important;
            display: block !important;
            padding: 0 !important;
            margin: 0 !important;
            z-index: 99999 !important;
        }

        .cert-modal-dialog {
            max-width: none !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            overflow: visible !important;
        }

        .cert-container {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 12pt !important;
            line-height: 1.8 !important;
        }

        .surat-kop-title { font-size: 14pt !important; }
        .surat-kop-sub   { font-size: 12pt !important; }
        .surat-judul     { font-size: 14pt !important; }
        .surat-p         { font-size: 12pt !important; }
        .surat-data-table{ font-size: 12pt !important; }
        .surat-ttd-kota, .surat-ttd-jabatan,
        .surat-ttd-nama, .surat-ttd-under { font-size: 12pt !important; }
        .surat-ttd-space { height: 60px !important; }
    }
</style>

<script>
    const certModal = document.getElementById('certificate-modal');

    function openCertificateModal(no, nama, nim, univ, cabang, predikat, nilai, tgl, previewUrl) {
        if (!certModal) return;
        const previewFrame = document.getElementById('certificate-preview-frame');
        if (previewFrame) previewFrame.src = previewUrl;
        document.getElementById('cert-modal-no').textContent = no;
        document.getElementById('cert-modal-nama').textContent = nama;
        document.getElementById('cert-modal-nim').textContent = nim;
        document.getElementById('cert-modal-univ').textContent = univ;
        document.getElementById('cert-modal-cabang').textContent = cabang;
        document.getElementById('cert-modal-nilai').textContent = nilai;
        
        let predLabel = predikat;
        if (predikat === 'A') predLabel = 'A (Sangat Baik)';
        else if (predikat === 'B') predLabel = 'B (Baik)';
        else if (predikat === 'C') predLabel = 'C (Cukup)';
        document.getElementById('cert-modal-predikat').textContent = predLabel;
        document.getElementById('cert-modal-tgl').textContent = tgl;

        certModal.classList.add('active');
        certModal.setAttribute('aria-hidden', 'false');
    }

    function closeCertificateModal() {
        if (!certModal) return;
        certModal.classList.remove('active');
        certModal.setAttribute('aria-hidden', 'true');
    }

    function printCertificate(no, nama, nim, univ, cabang, predikat, nilai, tgl) {
        openCertificateModal(no, nama, nim, univ, cabang, predikat, nilai, tgl);
        setTimeout(() => {
            window.print();
        }, 300);
    }

    if (certModal) {
        certModal.addEventListener('click', (e) => {
            if (e.target === certModal) closeCertificateModal();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' || e.key === 'Esc') {
                if (certModal.classList.contains('active')) closeCertificateModal();
            }
        });
    }
</script>
@endsection
