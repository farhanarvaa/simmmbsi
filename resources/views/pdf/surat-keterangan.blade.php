<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 10mm 16mm 10mm 18mm; }
        * { box-sizing: border-box; }
        body { color: #111; font-family: DejaVu Sans, sans-serif; font-size: 9.5pt; line-height: 1.22; }
        .header { border-bottom: 2px solid #111; padding-bottom: 5px; margin-bottom: 10px; }
        .header-table, .data-table { width: 100%; border-collapse: collapse; }
        .header-logo { width: 90px; vertical-align: middle; }
        .header-logo img { width: 68px; height: auto; }
        .header-name { vertical-align: middle; font-size: 14pt; font-weight: bold; letter-spacing: .5px; }
        .header-name small { display: block; font-size: 8pt; font-weight: normal; letter-spacing: 0; margin-top: 1px; }
        .title { text-align: center; margin-bottom: 10px; }
        .title h1 { font-family: "DejaVu Serif", serif; font-size: 12.5pt; margin: 0; text-decoration: underline; }
        .title div { font-size: 9.5pt; margin-top: 2px; }
        p { text-align: justify; margin: 0 0 6px; }
        .intro { margin-bottom: 4px; }
        .data-table { margin: 1px 0 7px 22px; width: calc(100% - 22px); }
        .data-table td { vertical-align: top; padding: 1px 0; }
        .label { width: 125px; }
        .separator { width: 18px; text-align: center; }
        .closing { margin-top: 4px; }
        .signature { width: 270px; margin: 10px 0 0 auto; text-align: left; page-break-before: avoid; page-break-inside: avoid; }
        .signature .company { margin-bottom: 2px; }
        .signature .space { height: 56px; }
        .signature .name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-logo"><img src="{{ $logoData }}" alt="Logo BSI"></td>
                <td class="header-name">BANK SYARIAH INDONESIA<small>PT BANK SYARIAH INDONESIA (PERSERO) TBK.</small></td>
            </tr>
        </table>
    </div>

    <div class="title">
        <h1>SURAT KETERANGAN</h1>
        <div>{{ $surat->nomor_surat }}</div>
    </div>

    <p class="intro">Yang bertandatangan di bawah ini saya,</p>
    <table class="data-table">
        <tr><td class="label">Nama</td><td class="separator">:</td><td>Hafid Syafriadi</td></tr>
        <tr><td class="label">Jabatan</td><td class="separator">:</td><td>Area Operation &amp; Service Manager</td></tr>
    </table>

    <p class="intro">Memberikan keterangan bahwa:</p>
    <table class="data-table">
        <tr><td class="label">Nama</td><td class="separator">:</td><td>{{ $mahasiswa->nama_lengkap }}</td></tr>
        <tr><td class="label">NIM</td><td class="separator">:</td><td>{{ $mahasiswa->nim }}</td></tr>
        <tr><td class="label">Prodi/Fakultas</td><td class="separator">:</td><td>{{ $mahasiswa->jurusan }}/{{ $mahasiswa->fakultas }}</td></tr>
        <tr><td class="label">Universitas</td><td class="separator">:</td><td>{{ $mahasiswa->universitas }}</td></tr>
        <tr><td class="label">Cabang/Unit Penempatan</td><td class="separator">:</td><td>{{ $mahasiswa->cabang?->nama ?? 'Belum ditentukan' }}</td></tr>
    </table>

    <p>Bahwa yang bersangkutan di atas adalah mahasiswa yang telah melaksanakan magang/Praktik Kerja Lapangan (PKL) di PT Bank Syariah Indonesia (Persero) Tbk Area Yogyakarta selama {{ $duration }} terhitung dari tanggal {{ $startDate }} sampai dengan tanggal {{ $endDate }}.</p>
    <p>Selama melaksanakan magang/Praktik Kerja Lapangan (PKL), yang bersangkutan telah melaksanakan tugas dan tanggung jawab dengan baik serta aktif mengikuti kegiatan administrasi selama magang.</p>
    <p class="closing">Demikian surat keterangan ini dibuat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya.</p>

    <div class="signature">
        <div class="company">PT. BANK SYARIAH INDONESIA (PERSERO) TBK.<br>AREA YOGYAKARTA</div>
        <div class="space"></div>
        <div class="name">Hafid Syafriadi</div>
        <div>AOSM</div>
    </div>
</body>
</html>
