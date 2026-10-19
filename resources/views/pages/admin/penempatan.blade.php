@extends('layouts.app')

@section('title', 'SIMMM - Penempatan Magang BSI')

@section('content')
<x-topbar :title="$isAdmin ? 'Penempatan Cabang/Unit & Surat Penerimaan Magang' : 'Surat Penerimaan Magang'" :subtitle="$isAdmin ? 'Kelola alokasi unit kerja, cetak surat penerimaan resmi, dan kelola periode magang mahasiswa BSI.' : 'Pratinjau dan unduh surat penerimaan magang resmi dari Bank Syariah Indonesia Area Yogyakarta.'" />

@if (session('success'))
    <div class="flash">
        <div style="display:flex; align-items:center; gap:10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
    </div>
@endif

{{-- KARTU SURAT PENERIMAAN MAGANG (Untuk Mahasiswa) --}}
@if(!$isAdmin)
    @php
        $mhsSaya = auth()->user()->mahasiswa;
        $sudahDiterima = $mhsSaya && in_array($mhsSaya->status_pendaftaran, ['diterima','ditempatkan']);
        $noSuratSaya = $mhsSaya ? 'BSI-YOG/PNR/'.date('Y').'/'.str_pad($mhsSaya->id, 4, '0', STR_PAD_LEFT) : '-';
        $tglSuratSaya = now()->translatedFormat('d F Y');
    @endphp
    @if($sudahDiterima)
    <div class="card" style="border:2px solid #0A3F37;background:linear-gradient(135deg,#f0faf8 0%,#fff 100%);position:relative;overflow:hidden;margin-bottom:28px;">
        <div style="position:absolute;right:-30px;top:-30px;width:160px;height:160px;background:radial-gradient(circle,rgba(10,63,55,.07) 0%,rgba(10,63,55,0) 70%);border-radius:50%;pointer-events:none;"></div>
        <div class="card-header" style="border-bottom:1px dashed #0A3F37;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:44px;height:44px;border-radius:10px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;color:var(--primary);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div>
                    <h3 style="color:var(--primary-dark);font-size:18px;">Surat Penerimaan Magang</h3>
                    <div class="card-sub">Bank Syariah Indonesia Area Yogyakarta</div>
                </div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:18px;padding:18px;background:var(--surface);border:1px solid var(--line);border-radius:10px;margin-bottom:22px;">
            <div>
                <div style="font-size:11px;color:var(--muted);text-transform:uppercase;font-weight:700;letter-spacing:.06em;margin-bottom:4px;">Nomor Surat</div>
                <div class="mono" style="font-size:13px;font-weight:700;color:var(--primary-dark);">{{ $noSuratSaya }}</div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--muted);text-transform:uppercase;font-weight:700;letter-spacing:.06em;margin-bottom:4px;">Nama Mahasiswa</div>
                <div style="font-size:14px;font-weight:700;color:var(--ink);">{{ $mhsSaya->nama_lengkap }}</div>
                <div style="font-size:12px;color:var(--muted);">NIM: {{ $mhsSaya->nim }}</div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--muted);text-transform:uppercase;font-weight:700;letter-spacing:.06em;margin-bottom:4px;">Cabang/Unit Penempatan</div>
                <div style="font-size:13px;font-weight:600;color:var(--ink);">{{ $mhsSaya->cabang?->nama ?: 'Akan ditentukan' }}</div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--muted);text-transform:uppercase;font-weight:700;letter-spacing:.06em;margin-bottom:4px;">Periode Magang</div>
                <div style="font-size:13px;font-weight:600;color:var(--ink);">
                    @if($mhsSaya->tanggal_mulai && $mhsSaya->tanggal_selesai)
                        {{ \Carbon\Carbon::parse($mhsSaya->tanggal_mulai)->translatedFormat('d F Y') }} &mdash; {{ \Carbon\Carbon::parse($mhsSaya->tanggal_selesai)->translatedFormat('d F Y') }}
                    @else
                        <span style="color:var(--muted);font-style:italic;font-size:12px;">Belum ditentukan</span>
                    @endif
                </div>
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:12px;align-items:center;">
            <button type="button" class="btn btn-ghost" id="btn-lihat-surat-saya"
                onclick="openSuratModal('{{ addslashes($mhsSaya->nama_lengkap) }}','{{ $mhsSaya->nim }}','{{ addslashes($mhsSaya->universitas) }}','{{ addslashes($mhsSaya->jurusan) }}','{{ addslashes($mhsSaya->cabang?->nama ?? "Akan ditentukan") }}','{{ addslashes($mhsSaya->no_hp ?? "-") }}','{{ $mhsSaya->tanggal_mulai ? \Carbon\Carbon::parse($mhsSaya->tanggal_mulai)->translatedFormat("d F Y") : "-" }}','{{ $mhsSaya->tanggal_selesai ? \Carbon\Carbon::parse($mhsSaya->tanggal_selesai)->translatedFormat("d F Y") : "-" }}','{{ $noSuratSaya }}','{{ $tglSuratSaya }}')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Lihat Surat
            </button>
            <button type="button" class="btn btn-solid" id="btn-cetak-surat-saya"
                onclick="printSuratPenerimaan('{{ addslashes($mhsSaya->nama_lengkap) }}','{{ $mhsSaya->nim }}','{{ addslashes($mhsSaya->universitas) }}','{{ addslashes($mhsSaya->jurusan) }}','{{ addslashes($mhsSaya->cabang?->nama ?? "Akan ditentukan") }}','{{ addslashes($mhsSaya->no_hp ?? "-") }}','{{ $mhsSaya->tanggal_mulai ? \Carbon\Carbon::parse($mhsSaya->tanggal_mulai)->translatedFormat("d F Y") : "-" }}','{{ $mhsSaya->tanggal_selesai ? \Carbon\Carbon::parse($mhsSaya->tanggal_selesai)->translatedFormat("d F Y") : "-" }}','{{ $noSuratSaya }}','{{ $tglSuratSaya }}')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Cetak / Unduh PDF
            </button>
        </div>
    </div>
    @else
    <div class="card">
        <div class="empty-state">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3>Surat Penerimaan Belum Diterbitkan</h3>
            <p>Surat Penerimaan Magang resmi akan otomatis diterbitkan dan dapat diunduh setelah berkas pendaftaran Anda diverifikasi dan dinyatakan <strong>Diterima</strong>.</p>
        </div>
    </div>
    @endif
@endif

@if($isAdmin)
<div class="card">
    <div class="card-header">
        <div>
            <h3>Daftar Mahasiswa Diterima &amp; Penempatan</h3>
            <div class="card-sub">Mahasiswa yang telah lolos seleksi berkas pendaftaran dan siap/sedang ditempatkan.</div>
        </div>
    </div>

    @if(!isset($mahasiswas) || $mahasiswas->isEmpty())
        <div class="empty-state">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m0 0v-5a2 2 0 012-2h2a2 2 0 012 2v5" />
            </svg>
            <h3>Belum Ada Mahasiswa Diterima</h3>
            <p>Mahasiswa yang berkasnya telah disetujui/diterima akan tampil di sini untuk ditentukan cabang/unit penempatannya.</p>
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>NIM &amp; Nama</th>
                        <th>Perguruan Tinggi / Jurusan</th>
                        <th>Cabang/Unit Penempatan</th>
                        <th>Periode Magang</th>
                        <th>Status</th>
                        <th>Surat Penerimaan</th>
                        @if($isAdmin)<th>Aksi Penempatan</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($mahasiswas as $mhs)
                        @php
                            $noSuratMhs = 'BSI-YOG/PNR/'.date('Y').'/'.str_pad($mhs->id, 4, '0', STR_PAD_LEFT);
                            $tglSuratMhs = now()->translatedFormat('d F Y');
                            $tglMulaiMhs = $mhs->tanggal_mulai ? \Carbon\Carbon::parse($mhs->tanggal_mulai)->translatedFormat('d F Y') : '-';
                            $tglSelesaiMhs = $mhs->tanggal_selesai ? \Carbon\Carbon::parse($mhs->tanggal_selesai)->translatedFormat('d F Y') : '-';
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $mhs->nama_lengkap }}</strong>
                                <div style="font-size:11.5px;color:var(--muted);" class="mono">NIM: {{ $mhs->nim }}</div>
                            </td>
                            <td>
                                <div>{{ $mhs->universitas }}</div>
                                <div style="font-size:12px;color:var(--muted);">{{ $mhs->jurusan }}</div>
                            </td>
                            <td>
                                @if($mhs->cabang)
                                    <span class="badge badge-ok" style="font-weight:700;">{{ $mhs->cabang->nama }}</span>
                                @else
                                    <span style="color:var(--muted);font-size:12px;font-style:italic;">Belum Ditentukan</span>
                                @endif
                            </td>
                            <td>
                                @if($mhs->tanggal_mulai && $mhs->tanggal_selesai)
                                    <div style="font-size:12.5px;">
                                        {{ \Carbon\Carbon::parse($mhs->tanggal_mulai)->format('d/m/Y') }} &mdash; {{ \Carbon\Carbon::parse($mhs->tanggal_selesai)->format('d/m/Y') }}
                                    </div>
                                @else
                                    <span style="color:var(--muted);font-size:12px;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($mhs->status_pendaftaran === 'ditempatkan')
                                    <span class="badge badge-ok">DITEMPATKAN</span>
                                @else
                                    <span class="badge badge-wait">MENUNGGU PENEMPATAN</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-ghost btn-sm"
                                    onclick="openSuratModal('{{ addslashes($mhs->nama_lengkap) }}','{{ $mhs->nim }}','{{ addslashes($mhs->universitas) }}','{{ addslashes($mhs->jurusan) }}','{{ addslashes($mhs->cabang?->nama ?? "") }}','{{ addslashes($mhs->no_hp ?? "-") }}','{{ $tglMulaiMhs }}','{{ $tglSelesaiMhs }}','{{ $noSuratMhs }}','{{ $tglSuratMhs }}')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    Lihat / Cetak
                                </button>
                            </td>
                            @if($isAdmin)
                            <td>
                                <button type="button" class="btn btn-ghost btn-sm"
                                    onclick="openPenempatanModal('{{ $mhs->id }}','{{ addslashes($mhs->nama_lengkap) }}','{{ $mhs->id_cabang ?? "" }}','{{ $mhs->tanggal_mulai ? \Carbon\Carbon::parse($mhs->tanggal_mulai)->format("Y-m-d") : "" }}','{{ $mhs->tanggal_selesai ? \Carbon\Carbon::parse($mhs->tanggal_selesai)->format("Y-m-d") : "" }}')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    <span>Atur Cabang/Unit</span>
                                </button>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($mahasiswas->hasPages())
            <div style="margin-top:20px;display:flex;justify-content:center;">
                {{ $mahasiswas->links() }}
            </div>
        @endif
    @endif
</div>
@endif

{{-- MODAL PREVIEW SURAT PENERIMAAN MAGANG --}}
<div id="surat-penerimaan-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box sp-dialog" role="dialog" aria-modal="true">
        <div class="sp-dialog-header">
            <div class="modal-title" style="margin:0;font-size:17px;font-weight:700;">Pratinjau Surat Penerimaan Magang</div>
            <button type="button" onclick="closeSuratModal()" aria-label="Tutup" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:24px;line-height:1;padding:0 4px;">&times;</button>
        </div>
        <div id="printable-surat-penerimaan" class="sp-surat">
            <div class="sp-kop">
                <img src="{{ asset('images/logo_bsi.png') }}" alt="Logo BSI" class="sp-logo">
                <div class="sp-kop-text">
                    <div class="sp-kop-nama">PT BANK SYARIAH INDONESIA TBK</div>
                    <div class="sp-kop-cabang">AREA YOGYAKARTA</div>
                    <div class="sp-kop-alamat">Jl. Jend. Sudirman No.57, Gondokusuman, Yogyakarta 55233 &nbsp;|&nbsp; Telp. (0274) 512345</div>
                </div>
            </div>
            <div class="sp-garis-kop"></div>

            <div class="sp-judul">SURAT PERNYATAAN</div>
            <div class="sp-subjudul">(PRAKTIK KERJA/MAGANG)</div>

            <div class="sp-isi">
                <p>Saya yang bertanda tangan di bawah ini:</p>
                <div class="sp-data-mhs">
                    <div class="sp-data-row"><div class="sp-data-lbl">Nama</div><div class="sp-data-sep">:</div><div class="sp-data-val" id="sp-nama-mhs">&ndash;</div></div>
                    <div class="sp-data-row"><div class="sp-data-lbl">No. Identitas (NIS/NIM)</div><div class="sp-data-sep">:</div><div class="sp-data-val" id="sp-nim-mhs">&ndash;</div></div>
                    <div class="sp-data-row"><div class="sp-data-lbl">Alamat Rumah</div><div class="sp-data-sep">:</div><div class="sp-data-val sp-blank-line">&nbsp;</div></div>
                    <div class="sp-data-row"><div class="sp-data-lbl">Nomor Telepon Rumah &amp; HP</div><div class="sp-data-sep">:</div><div class="sp-data-val" id="sp-no-hp-mhs">&ndash;</div></div>
                    <div class="sp-data-row"><div class="sp-data-lbl">Institusi</div><div class="sp-data-sep">:</div><div class="sp-data-val" id="sp-univ-mhs">&ndash;</div></div>
                    <div class="sp-data-row"><div class="sp-data-lbl">Jurusan</div><div class="sp-data-sep">:</div><div class="sp-data-val" id="sp-jurusan-mhs">&ndash;</div></div>
                    <div class="sp-data-row"><div class="sp-data-lbl">Alamat Institusi</div><div class="sp-data-sep">:</div><div class="sp-data-val sp-blank-line">&nbsp;</div></div>
                </div>

                <p><strong>Penanggung Jawab Institusi:</strong></p>
                <div class="sp-data-mhs">
                    <div class="sp-data-row"><div class="sp-data-lbl">Nama Dosen/Guru Penanggung Jawab</div><div class="sp-data-sep">:</div><div class="sp-data-val sp-blank-line">&nbsp;</div></div>
                    <div class="sp-data-row"><div class="sp-data-lbl">Jabatan</div><div class="sp-data-sep">:</div><div class="sp-data-val sp-blank-line">&nbsp;</div></div>
                    <div class="sp-data-row"><div class="sp-data-lbl">No. HP</div><div class="sp-data-sep">:</div><div class="sp-data-val sp-blank-line">&nbsp;</div></div>
                </div>

                <p><strong>Penanggung Jawab di PT Bank Syariah Indonesia:</strong></p>
                <div class="sp-data-mhs">
                    <div class="sp-data-row"><div class="sp-data-lbl">Nama</div><div class="sp-data-sep">:</div><div class="sp-data-val">Hafid Syafriadi</div></div>
                    <div class="sp-data-row"><div class="sp-data-lbl">Jabatan</div><div class="sp-data-sep">:</div><div class="sp-data-val">Area Operation &amp; Service Manager</div></div>
                    <div class="sp-data-row"><div class="sp-data-lbl">Cabang/Unit Penempatan</div><div class="sp-data-sep">:</div><div class="sp-data-val" id="sp-cabang-mhs">&ndash;</div></div>
                </div>

                <p>Dalam rangka melaksanakan praktik kerja/magang di Cabang/Unit Penempatan: <strong id="sp-cabang-paragraf">&ndash;</strong>, terhitung mulai tanggal <strong id="sp-mulai-mhs">&ndash;</strong> s.d. <strong id="sp-selesai-mhs">&ndash;</strong>, di PT Bank Syariah Indonesia, dengan ini menyatakan sanggup untuk memenuhi ketentuan-ketentuan sebagai berikut:</p>
                <ol class="sp-ol">
                    <li>Tujuan praktik kerja/magang semata-mata untuk kepentingan ilmiah, merahasiakan informasi yang diperoleh</li>
                    <li>Tidak melanggar ketentuan rahasia Bank sesuai UU No. 21 Tahun 2008 tentang perbankan syariah</li>
                    <li>Materi relevan dengan kegiatan Bank, tidak menyangkut hal rawan/membahayakan kepentingan dan keamanan PT Bank Syariah Indonesia</li>
                    <li>Pelaksanaan tidak mengganggu kegiatan operasional Group/Regional Office/KC/KCP tempat magang dilaksanakan</li>
                    <li>PT Bank Syariah Indonesia hanya menjawab materi kuesioner yang isinya sesuai outline yang disetujui</li>
                    <li>PT Bank Syariah Indonesia berwenang menerbitkan surat keterangan setelah penelitian memastikan tidak menyimpang/merugikan kepentingan perusahaan</li>
                    <li>Waktu praktik kerja/magang Senin s.d. Jumat, pukul 07:45–17:00, istirahat 1 jam (12:00–13:00), tanpa kompensasi lembur</li>
                    <li>Tidak masuk tanpa keterangan 3 hari berturut-turut dianggap mengundurkan diri, tidak berhak kompensasi dan surat keterangan</li>
                    <li>PT Bank Syariah Indonesia berhak melaporkan aktivitas peserta kepada penanggung jawab institusi</li>
                </ol>
                <p>Demikian Surat Pernyataan ini saya buat dengan sesungguhnya tanpa tekanan maupun paksaan dari pihak manapun. Selanjutnya apabila dikemudian hari ternyata terdapat penyimpangan atas pelaksanaan pernyataan-pernyataan yang telah saya buat tersebut di atas, maka saya bersedia untuk menerima sanksi sesuai ketentuan perundang-undangan yang berlaku.</p>
            </div>

            <div class="sp-footer-ttd">
                <div class="sp-ttd">
                    <div class="sp-ttd-label">Mengetahui:</div>
                    <div class="sp-ttd-org">PT BANK SYARIAH INDONESIA Tbk</div>
                    <div class="sp-ttd-space"></div>
                    <div class="sp-ttd-name">(Hafid Syafriadi)</div>
                </div>
                <div class="sp-ttd">
                    <div class="sp-ttd-label">Yogyakarta, <span id="sp-tgl-surat">-</span></div>
                    <div class="sp-ttd-label">Hormat Saya,</div>
                    <div class="sp-ttd-space"></div>
                    <div class="sp-ttd-name" id="sp-ttd-penerima">(Nama Mahasiswa)</div>
                </div>
            </div>
        </div>
        <div class="modal-actions sp-dialog-footer">
            <button type="button" class="btn btn-ghost" onclick="closeSuratModal()">Tutup</button>
            <button type="button" class="btn btn-solid" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>
</div>

@if($isAdmin)
<div id="penempatan-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:500px;" role="dialog" aria-modal="true">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;border-bottom:1px solid var(--line);padding-bottom:12px;">
            <div>
                <div class="modal-title" style="margin:0;font-size:18px;font-weight:700;color:var(--primary-dark);">Atur Penempatan Cabang/Unit</div>
                <div id="modal-mhs-nama" style="font-size:12.5px;color:var(--muted);margin-top:2px;">Mahasiswa: -</div>
            </div>
            <button type="button" onclick="closePenempatanModal()" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:24px;line-height:1;">&times;</button>
        </div>
        <form id="penempatan-form" action="" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom:14px;">
                <label class="form-label">Pilih Cabang/Unit Penempatan <span style="color:var(--danger);">*</span></label>
                <select name="id_cabang" id="modal-cabang-select" required style="margin-bottom:8px;">
                    <option value="">-- Pilih Cabang/Unit --</option>
                    @foreach($cabangs as $cabang)
                        <option value="{{ $cabang->id }}">{{ $cabang->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
                <div>
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="modal-tanggal-mulai">
                </div>
                <div>
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="modal-tanggal-selesai">
                </div>
            </div>
            <div class="modal-actions" style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid var(--line);padding-top:14px;">
                <button type="button" class="btn btn-ghost" onclick="closePenempatanModal()">Batal</button>
                <button type="submit" class="btn btn-solid">Simpan Penempatan</button>
            </div>
        </form>
    </div>
</div>
@endif

<style>
    .sp-dialog{max-width:760px;width:95%;padding:24px;max-height:92vh;overflow-y:auto;}
    .sp-dialog-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;border-bottom:1px solid var(--line);padding-bottom:12px;}
    .sp-dialog-footer{display:flex;justify-content:flex-end;gap:12px;margin-top:18px;}
    .sp-surat{background:#fff;border:1px solid #d1d5db;padding:30px 36px 28px;font-family:'Times New Roman',Times,serif;font-size:12.5px;color:#111;line-height:1.65;border-radius:3px;}
    .sp-kop{display:flex;align-items:center;gap:16px;margin-bottom:10px;}
    .sp-logo{height:50px;object-fit:contain;flex-shrink:0;}
    .sp-kop-text{flex:1;}
    .sp-kop-nama{font-family:'Plus Jakarta Sans','Inter',sans-serif;font-size:13.5px;font-weight:800;color:#0A3F37;letter-spacing:.05em;}
    .sp-kop-cabang{font-family:'Plus Jakarta Sans','Inter',sans-serif;font-size:11.5px;font-weight:600;color:#0A3F37;}
    .sp-kop-alamat{font-size:10px;color:#6b7280;margin-top:2px;}
    .sp-garis-kop{border-top:4px solid #0A3F37;border-bottom:1.5px solid #C89B3C;margin-bottom:18px;}
    .sp-judul{text-align:center;font-size:15px;font-weight:700;text-decoration:underline;letter-spacing:.04em;margin:4px 0 2px;}
    .sp-subjudul{text-align:center;font-size:12px;font-weight:700;margin-bottom:18px;}
    .sp-nomor-blok{margin-bottom:10px;}
    .sp-row-meta{display:flex;font-size:12.5px;margin-bottom:2px;}
    .sp-lbl{width:80px;flex-shrink:0;}
    .sp-sep{width:18px;flex-shrink:0;}
    .sp-tanggal{text-align:right;font-size:12.5px;margin-bottom:18px;color:#374151;}
    .sp-kepada{font-size:12.5px;line-height:1.8;margin-bottom:18px;}
    .sp-isi p{font-size:12.5px;margin-bottom:10px;text-align:justify;}
    .sp-data-mhs{margin:14px 0 14px 16px;padding-left:14px;border-left:3px solid #C89B3C;}
    .sp-data-row{display:flex;font-size:12.5px;margin-bottom:3px;}
    .sp-data-lbl{width:140px;flex-shrink:0;color:#55625D;}
    .sp-data-sep{width:14px;flex-shrink:0;}
    .sp-data-val{flex:1;font-weight:600;color:#111;}
    .sp-blank-line{min-height:18px;border-bottom:1px solid #9ca3af;}
    .sp-ol{font-size:12px;padding-left:18px;margin:8px 0 12px;line-height:1.7;text-align:justify;}
    .sp-ol li{margin-bottom:3px;}
    .sp-footer-ttd{display:flex;justify-content:space-between;margin-top:28px;}
    .sp-ttd{text-align:center;min-width:170px;}
    .sp-ttd-label{font-size:12.5px;color:#374151;margin-bottom:2px;}
    .sp-ttd-org{font-size:11.5px;font-weight:700;color:#0A3F37;}
    .sp-ttd-org-sub{font-size:10.5px;color:#55625D;margin-bottom:4px;}
    .sp-ttd-space{height:55px;border-bottom:1px solid #9ca3af;margin:4px 12px 4px;}
    .sp-ttd-name{font-size:12.5px;font-weight:700;text-decoration:underline;color:#0A3F37;margin-top:4px;}
    .sp-ttd-role{font-size:10.5px;color:#6b7280;}

    @page{size:A4 portrait;margin:18mm 20mm;}
    @media print{
        *,*:before,*:after{-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}
        html,body{width:210mm !important;margin:0 !important;padding:0 !important;background:#fff !important;}
        .sidebar,.sidebar-overlay,.mobile-header,.topbar,.card,.flash,#flash-container,.sp-dialog-header,.sp-dialog-footer,#penempatan-modal,#custom-confirm-modal,#confirm-dialog-overlay{display:none !important;}
        .app-shell,.app-layout,.main-content,.main{margin:0 !important;padding:0 !important;display:block !important;}
        .modal-overlay{position:fixed !important;top:0 !important;left:0 !important;width:210mm !important;background:#fff !important;display:block !important;padding:0 !important;margin:0 !important;z-index:99999 !important;overflow:visible !important;}
        .sp-dialog{max-width:none !important;width:210mm !important;max-height:none !important;padding:0 !important;margin:0 !important;border:none !important;box-shadow:none !important;background:transparent !important;overflow:visible !important;}
        .sp-surat{border:none !important;padding:0 !important;border-radius:0 !important;font-size:11.5pt !important;}
        .sp-garis-kop{border-top:4pt solid #0A3F37 !important;border-bottom:1.5pt solid #C89B3C !important;}
        .sp-data-mhs{border-left:3pt solid #C89B3C !important;}
        #certificate-modal{display:none !important;}
        #surat-penerimaan-modal.active{display:block !important;}
    }
</style>

<script>
    const spModal = document.getElementById('surat-penerimaan-modal');

    function openSuratModal(nama, nim, univ, jurusan, cabang, noHp, mulai, selesai, noSurat, tglSurat) {
        if (!spModal) return;
        document.getElementById('sp-tgl-surat').textContent = tglSurat || '-';
        document.getElementById('sp-nama-mhs').textContent = nama;
        document.getElementById('sp-nim-mhs').textContent = nim;
        document.getElementById('sp-no-hp-mhs').textContent = noHp || '-';
        document.getElementById('sp-jurusan-mhs').textContent = jurusan;
        document.getElementById('sp-univ-mhs').textContent = univ;
        document.getElementById('sp-cabang-mhs').textContent = cabang || 'Akan Ditentukan';
        document.getElementById('sp-cabang-paragraf').textContent = cabang || 'Akan Ditentukan';
        document.getElementById('sp-mulai-mhs').textContent = mulai;
        document.getElementById('sp-selesai-mhs').textContent = selesai;
        document.getElementById('sp-ttd-penerima').textContent = '(' + nama + ')';
        spModal.classList.add('active');
        spModal.setAttribute('aria-hidden', 'false');
    }

    function closeSuratModal() {
        if (!spModal) return;
        spModal.classList.remove('active');
        spModal.setAttribute('aria-hidden', 'true');
    }

    function printSuratPenerimaan(nama, nim, univ, jurusan, cabang, noHp, mulai, selesai, noSurat, tglSurat) {
        openSuratModal(nama, nim, univ, jurusan, cabang, noHp, mulai, selesai, noSurat, tglSurat);
        setTimeout(() => { window.print(); }, 300);
    }

    if (spModal) {
        spModal.addEventListener('click', e => {
            if (e.target === spModal) closeSuratModal();
        });
        document.addEventListener('keydown', e => {
            if ((e.key === 'Escape' || e.key === 'Esc') && spModal.classList.contains('active')) closeSuratModal();
        });
    }

    @if($isAdmin)
    const penempatanModal = document.getElementById('penempatan-modal');
    const penempatanForm  = document.getElementById('penempatan-form');
    const modalNama       = document.getElementById('modal-mhs-nama');
    const cabangSelect    = document.getElementById('modal-cabang-select');
    const tglMulai        = document.getElementById('modal-tanggal-mulai');
    const tglSelesai      = document.getElementById('modal-tanggal-selesai');

    function openPenempatanModal(id, nama, idCabang, mulai, selesai) {
        if (!penempatanModal) return;
        penempatanForm.action = `/penempatan/${id}`;
        modalNama.textContent = `Mahasiswa: ${nama}`;
        cabangSelect.value = idCabang || '';
        tglMulai.value = mulai || ''; tglSelesai.value = selesai || '';
        penempatanModal.classList.add('active'); penempatanModal.setAttribute('aria-hidden', 'false');
    }

    function closePenempatanModal() {
        if (!penempatanModal) return;
        penempatanModal.classList.remove('active'); penempatanModal.setAttribute('aria-hidden', 'true');
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (penempatanModal) {
            penempatanModal.addEventListener('click', e => {
                if (e.target === penempatanModal) closePenempatanModal();
            });
        }
    });
    @endif
</script>
@endsection
