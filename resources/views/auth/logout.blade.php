<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMMM — Logout</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg: #FAF9F4;
            --surface: #FFFFFF;
            --ink: #17231F;
            --ink-soft: #3C4A45;
            --muted: #6E7B76;
            --primary: #0E5C4F;
            --primary-dark: #0A3F37;
            --primary-light: #E4EEEB;
            --accent: #C89B3C;
            --line: #E3E0D3;
            --radius: 3px;
        }
        *{ box-sizing:border-box; }
        body{
            margin:0;
            min-height:100vh;
            width:100%;
            background: radial-gradient(500px 260px at 50% 0%, var(--primary-light), transparent 70%);
            color: var(--ink);
            font-family:'Inter', sans-serif;
            display:flex;
            align-items:center;
            justify-content:center;
            padding: clamp(32px, 5vw, 60px) clamp(18px, 4vw, 28px);
        }
        h2{ font-family:'Fraunces', serif; margin:0; }
        a{ color:inherit; text-decoration:none; }
        .logout-card{
            text-align:center;
            max-width: min(100%, 380px);
            width:100%;
        }
        .brand-mark{
            width:90px; height:32px; display:flex; align-items:center; justify-content:center;
            margin:0 auto 18px; overflow:visible;
        }
        .brand-mark img{ width:100%; height:100%; display:block; object-fit:contain; }
        .logout-stamp-wrap{ display:flex; justify-content:center; margin-bottom: 28px; }
        .logout-stamp-wrap .stamp{ animation: stamp-hit .5s ease both; }
        @keyframes stamp-hit{
            0%{ transform: scale(1.6) rotate(-4deg); opacity:0; }
            70%{ transform: scale(0.96) rotate(-9deg); opacity:1; }
            100%{ transform: scale(1) rotate(-9deg); opacity:1; }
        }
        .logout-card h2{ font-size:26px; font-weight:600; margin-bottom:10px; }
        .logout-card p{ color: var(--ink-soft); font-size:14.5px; line-height:1.6; margin-bottom:30px; }
        .logout-actions{ display:flex; gap:12px; justify-content:center; flex-wrap:wrap; }
        @media (max-width: 420px){
            .logout-actions{ flex-direction:column; }
            .logout-actions .btn{ width:100%; }
            .logout-meta{ flex-direction:column; gap:6px; }
        }
        .btn{
            display:inline-flex;
            justify-content:center;
            align-items:center;
            font-family:'Inter', sans-serif;
            font-size:14px;
            font-weight:600;
            padding:10px 20px;
            border-radius: var(--radius);
            cursor:pointer;
            border:1px solid transparent;
        }
        .btn-solid{ background: var(--primary); color:#fff; }
        .btn-ghost{ background:transparent; color: var(--primary-dark); border-color: var(--line); }
        .logout-meta{
            margin-top: 34px;
            padding-top: 22px;
            border-top: 1px solid var(--line);
            font-family:'IBM Plex Mono', monospace;
            font-size:11.5px;
            color: var(--muted);
            display:flex;
            justify-content:center;
            gap: 18px;
            flex-wrap:wrap;
        }
    </style>
</head>
<body>
    <div class="logout-card">
        <div class="brand-mark" aria-label="Logo BSI" role="img">
            <img src="{{ asset('images/logo_bsi.png') }}" alt="Logo BSI">
        </div>
        <div class="logout-stamp-wrap">
            <svg class="stamp" width="150" height="150" viewBox="0 0 150 150">
                <circle class="ring" cx="75" cy="75" r="64" stroke="var(--primary)" stroke-width="2" fill="none"/>
                <text x="75" y="70" text-anchor="middle" font-size="15" fill="var(--primary)" font-family="Fraunces, serif" font-weight="600">SELESAI</text>
                <path d="M56 88 l12 12 l26 -26" stroke="var(--primary)" stroke-width="3.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h2>Kamu sudah keluar</h2>
        <p>Sesi kamu telah diakhiri dengan aman. Data presensi dan logbook hari ini sudah tersimpan.</p>
        <div class="logout-actions">
            <a class="btn btn-solid" href="{{ route('login') }}">Masuk Kembali</a>
            <a class="btn btn-ghost" href="{{ route('home') }}">Kembali ke Beranda</a>
        </div>
        <div class="logout-meta">
            <span>KELUAR PUKUL 14:32</span>
            <span>·</span>
            <span>08 AGU 2026</span>
        </div>
    </div>
</body>
</html>
