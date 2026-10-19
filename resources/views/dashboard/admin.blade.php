@extends('layouts.app')

@section('title', 'SIMMM - Dashboard Admin/Mentor')

@section('content')
<div class="topbar">
    <div>
        <h1 id="admin-time-greeting">Dashboard Admin / Mentor</h1>
        <div class="subtitle">Panel kontrol, monitoring logbook, dan penilaian magang Bank Syariah Indonesia Area Yogyakarta.</div>
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

@php
    $totalDiterima = \App\Models\Mahasiswa::whereIn('status_pendaftaran', ['diterima', 'ditempatkan'])->count();
    $belumLaporRO = \App\Models\Mahasiswa::whereIn('status_pendaftaran', ['diterima', 'ditempatkan'])
        ->where('status_pelaporan_ro', 'belum_dilaporkan')
        ->count();
@endphp

@if($totalDiterima > 0 && $belumLaporRO > 0)
    <div style="background:var(--amber-bg); border:1px solid #e5cc9c; border-radius:10px; padding:16px 20px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; box-shadow:var(--shadow-sm);">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:36px; height:36px; border-radius:8px; background:rgba(184,134,58,0.15); display:flex; align-items:center; justify-content:center; color:var(--amber); flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div style="font-size:13.5px; color:var(--ink-soft); line-height:1.4;">
                <strong>Pengingat Pelaporan RO Pusat :</strong> <strong>{{ $belumLaporRO }}</strong> dari <strong>{{ $totalDiterima }}</strong> mahasiswa yang diterima periode ini belum dilaporkan ke RO Pusat (pencatatan administratif).
            </div>
        </div>
        <a href="{{ route('mahasiswas.index') }}" class="btn btn-warning btn-sm" style="flex-shrink:0;">Tinjau Pelaporan RO</a>
    </div>
@endif

{{-- Ringkasan Statistik --}}
<div class="card">
    <div class="card-header">
        <div>
            <h3>Ringkasan Mahasiswa Magang</h3>
            <div class="card-sub">Jumlah pendaftar dan progres verifikasi berkas.</div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:18px;">
        <div style="background:var(--bg); border:1px solid var(--line); border-radius:10px; padding:20px; transition:all 0.2s ease; position:relative; overflow:hidden;">
            <div style="font-family:'IBM Plex Mono',monospace; font-size:11px; color:var(--muted); text-transform:uppercase; font-weight:600; letter-spacing:0.05em;">TOTAL MAHASISWA</div>
            <div style="font-size:32px; font-weight:800; color:var(--primary-dark); margin-top:6px; letter-spacing:-0.03em;">{{ $totalMahasiswa }}</div>
            <div style="font-size:12px; color:var(--green); margin-top:4px; font-weight:600;">+{{ $mhsMingguIni }} minggu ini</div>
        </div>
        
        <div style="background:var(--bg); border:1px solid var(--line); border-radius:10px; padding:20px; transition:all 0.2s ease; position:relative; overflow:hidden;">
            <div style="font-family:'IBM Plex Mono',monospace; font-size:11px; color:var(--muted); text-transform:uppercase; font-weight:600; letter-spacing:0.05em;">PRESENSI HARI INI</div>
            <div style="font-size:32px; font-weight:800; color:var(--primary-dark); margin-top:6px; letter-spacing:-0.03em;">{{ $presensiHariIniCount }}</div>
            <div style="font-size:12px; color:var(--muted); margin-top:4px;">{{ $belumPresensiCount }} belum presensi</div>
        </div>

        <div style="background:var(--bg); border:1px solid var(--line); border-radius:10px; padding:20px; transition:all 0.2s ease; position:relative; overflow:hidden;">
            <div style="font-family:'IBM Plex Mono',monospace; font-size:11px; color:var(--muted); text-transform:uppercase; font-weight:600; letter-spacing:0.05em;">LOGBOOK TERVERIFIKASI</div>
            <div style="font-size:32px; font-weight:800; color:var(--primary-dark); margin-top:6px; letter-spacing:-0.03em;">{{ $logbookDisetujuiCount }}</div>
            <div style="font-size:12px; color:var(--amber); margin-top:4px; font-weight:600;">{{ $logbookDiajukanCount }} butuh peninjauan</div>
        </div>

        <div style="background:var(--bg); border:1px solid var(--line); border-radius:10px; padding:20px; transition:all 0.2s ease; position:relative; overflow:hidden;">
            <div style="font-family:'IBM Plex Mono',monospace; font-size:11px; color:var(--muted); text-transform:uppercase; font-weight:600; letter-spacing:0.05em;">SURAT KETERANGAN SELESAI</div>
            <div style="font-size:32px; font-weight:800; color:var(--primary-dark); margin-top:6px; letter-spacing:-0.03em;">{{ $sertifikatReadyCount }}</div>
            <div style="font-size:12px; color:var(--green); margin-top:4px; font-weight:600;">Siap terbit</div>
        </div>
    </div>
</div>

{{-- Section: Logbook Menunggu Ditinjau --}}
<div class="card">
    <div class="card-header">
        <div>
            <h3>Logbook Menunggu Ditinjau</h3>
            <div class="card-sub">Daftar logbook mahasiswa yang perlu diverifikasi oleh Admin/Mentor.</div>
        </div>
        <a href="{{ route('logbooks.index') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Mahasiswa</th>
                <th>Judul Kegiatan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingLogbooks as $logbook)
                <tr>
                    <td style="font-family:'IBM Plex Mono',monospace; font-size:12.5px;">{{ \Illuminate\Support\Carbon::parse($logbook->tanggal)->format('d/m/Y') }}</td>
                    <td><strong>{{ $logbook->user->name ?? '-' }}</strong></td>
                    <td>{{ \Illuminate\Support\Str::limit($logbook->judul, 60) }}</td>
                    <td><span class="badge badge-wait">DIAJUKAN</span></td>
                    <td>
                        <button type="button" class="btn btn-solid btn-sm" onclick="openLogbookDetailModal({
                            id: {{ $logbook->id }},
                            tanggal: '{{ \Illuminate\Support\Carbon::parse($logbook->tanggal)->format('d/m/Y') }}',
                            mahasiswa: '{{ e($logbook->user->name ?? '-') }}',
                            judul: '{{ e($logbook->judul) }}',
                            aktivitas: '{{ e($logbook->aktivitas) }}',
                            hasil: '{{ e($logbook->hasil ?? '-') }}',
                            updateUrl: '{{ route('logbooks.update', $logbook) }}'
                        })">Tinjau Logbook</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                            <p>Tidak ada logbook yang menunggu peninjauan saat ini.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Section: Penilaian Lapangan --}}
<div class="card">
    <div class="card-header">
        <div>
            <h3>Penilaian Lapangan</h3>
            <div class="card-sub">Input dan rekap nilai kinerja mahasiswa selama magang oleh Admin/Mentor.</div>
        </div>
        <a href="{{ route('penilaian.index') }}" class="btn btn-ghost btn-sm">Kelola Penilaian</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Mahasiswa</th>
                <th>Status Magang</th>
                <th>Nilai Lapangan</th>
                <th>Catatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswaPenilaian as $mhs)
                <tr>
                    <td>
                        <strong>{{ $mhs->nama_lengkap }}</strong>
                        <div style="font-size:12px; color:var(--muted); margin-top:2px; font-family:'IBM Plex Mono',monospace;">{{ $mhs->nim ?? '-' }}</div>
                    </td>
                    <td>
                        @if($mhs->status === 'selesai')
                            <span class="badge badge-ok">SELESAI</span>
                        @elseif($mhs->status === 'aktif' && $mhs->status_pendaftaran === 'ditempatkan')
                            <span class="badge badge-ok">AKTIF</span>
                        @elseif($mhs->status_pendaftaran === 'revisi')
                            <span class="badge badge-danger">REVISI</span>
                        @elseif($mhs->status_pendaftaran === 'ditolak')
                            <span class="badge badge-danger">DITOLAK</span>
                        @else
                            <span class="badge badge-wait">{{ strtoupper($mhs->status_pendaftaran ?? 'DIAJUKAN') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($mhs->penilaian)
                            <strong style="font-family:'IBM Plex Mono',monospace; font-size:14px; color:var(--ink);">{{ $mhs->penilaian->nilai_akhir }}</strong>
                            <span class="badge badge-ok" style="margin-left:4px;">Predikat {{ $mhs->penilaian->predikat }}</span>
                        @else
                            <span style="font-family:'IBM Plex Mono',monospace; font-size:13px; color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td style="color:var(--muted); font-size:13px;">
                        @if($mhs->penilaian)
                            {{ \Illuminate\Support\Str::limit($mhs->penilaian->catatan_mentor ?? 'Sudah dinilai', 45) }}
                        @else
                            Belum dinilai
                        @endif
                    </td>
                    <td>
                        @if($mhs->penilaian)
                            <a href="{{ route('penilaian.index') }}" class="btn btn-ghost btn-sm">Lihat Nilai</a>
                        @else
                            <a href="{{ route('penilaian.index') }}" class="btn btn-solid btn-sm">Input Nilai</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <p>Belum ada data mahasiswa untuk dinilai.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Detail & Peninjauan Logbook --}}
<div id="logbook-detail-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:540px;" role="dialog" aria-modal="true">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:18px;">
            <div>
                <div class="modal-title" style="margin:0;">Detail Logbook Kegiatan</div>
                <div style="font-size:12.5px; color:var(--muted); margin-top:2px;">Tinjau aktivitas yang dilaporkan oleh mahasiswa.</div>
            </div>
            <button type="button" onclick="closeLogbookDetailModal()" style="background:none; border:none; cursor:pointer; color:var(--muted); font-size:22px; padding:0; line-height:1;">&times;</button>
        </div>

        <div style="background:var(--bg); border:1px solid var(--line); border-radius:8px; padding:16px; margin-bottom:16px; font-size:13px; display:flex; flex-direction:column; gap:10px;">
            <div style="display:flex; justify-content:space-between; border-bottom:1px dashed var(--line); padding-bottom:8px;">
                <span style="color:var(--muted);">Mahasiswa:</span>
                <strong id="detail-mhs-nama">-</strong>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px dashed var(--line); padding-bottom:8px;">
                <span style="color:var(--muted);">Tanggal Kegiatan:</span>
                <span id="detail-mhs-tanggal" style="font-family:'IBM Plex Mono',monospace; font-weight:600;">-</span>
            </div>
            <div>
                <span style="color:var(--muted); display:block; margin-bottom:2px;">Judul Kegiatan:</span>
                <strong id="detail-mhs-judul" style="font-size:14px; color:var(--ink);">-</strong>
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:11.5px; font-family:'IBM Plex Mono',monospace; color:var(--muted); text-transform:uppercase; margin-bottom:6px;">Aktivitas Detail:</label>
            <div id="detail-mhs-aktivitas" style="background:var(--bg); border:1px solid var(--line); border-radius:6px; padding:12px; font-size:13.5px; line-height:1.5; color:var(--ink-soft); max-height:140px; overflow-y:auto; whitespace:pre-line;">-</div>
        </div>

        <div style="margin-bottom:22px;">
            <label style="display:block; font-size:11.5px; font-family:'IBM Plex Mono',monospace; color:var(--muted); text-transform:uppercase; margin-bottom:6px;">Hasil / Output:</label>
            <div id="detail-mhs-hasil" style="background:var(--bg); border:1px solid var(--line); border-radius:6px; padding:12px; font-size:13.5px; line-height:1.5; color:var(--ink-soft); max-height:100px; overflow-y:auto; whitespace:pre-line;">-</div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:12px; border-top:1px solid var(--line); padding-top:18px;">
            <button type="button" class="btn btn-ghost" onclick="closeLogbookDetailModal()">Tutup</button>
            
            <form id="form-revisi-logbook" method="POST" style="display:inline;" data-confirm="Tandai logbook ini perlu direvisi oleh mahasiswa?" data-title="Konfirmasi Minta Revisi" data-button="Minta Revisi" data-variant="warning">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="revisi">
                <button type="submit" class="btn btn-warning">Minta Revisi</button>
            </form>

            <form id="form-setujui-logbook" method="POST" style="display:inline;">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="disetujui">
                <button type="submit" class="btn btn-solid">Setujui Logbook</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openLogbookDetailModal(data) {
        document.getElementById('detail-mhs-nama').textContent = data.mahasiswa;
        document.getElementById('detail-mhs-tanggal').textContent = data.tanggal;
        document.getElementById('detail-mhs-judul').textContent = data.judul;
        document.getElementById('detail-mhs-aktivitas').textContent = data.aktivitas;
        document.getElementById('detail-mhs-hasil').textContent = data.hasil;

        document.getElementById('form-setujui-logbook').action = data.updateUrl;
        document.getElementById('form-revisi-logbook').action = data.updateUrl;

        document.getElementById('logbook-detail-modal').classList.add('active');
    }

    function closeLogbookDetailModal() {
        document.getElementById('logbook-detail-modal').classList.remove('active');
    }
</script>
@endsection
