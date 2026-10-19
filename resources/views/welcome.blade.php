<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMMM — Sistem Informasi Manajemen Mahasiswa Magang BSI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg: #F8FAFC;
            --surface: #FFFFFF;
            --ink: #0F172A;
            --ink-soft: #334155;
            --muted: #64748B;
            --primary: #00827B;
            --primary-dark: #005E59;
            --primary-light: #E6F6F5;
            --accent: #D97706;
            --accent-soft: #FEF3C7;
            --bsi-orange: #E87B24;
            --bsi-orange-light: #FFF7ED;
            --bsi-orange-soft: #FDBA74;
            --bsi-orange-subtle: rgba(232, 123, 36, 0.08);
            --line: #E2E8F0;
            --radius: 10px;
        }
        *{ box-sizing: border-box; }
        html{ scroll-behavior: smooth; }
        html, body{
            margin:0;
            padding:0;
            width:100%;
            min-height:100%;
            height:100%;
            background: var(--bg);
            color: var(--ink);
            font-family:'Plus Jakarta Sans', 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            overflow-x:hidden;
        }

        h1,h2,h3,h4{ font-family:'Plus Jakarta Sans', sans-serif; font-weight:700; margin:0; letter-spacing: -0.02em; }
        p{ margin:0; }
        a{ color: inherit; text-decoration:none; }
        .mono{ font-family:'IBM Plex Mono', monospace; letter-spacing: 0.04em; }

        @media (prefers-reduced-motion: reduce){
            *{ animation-duration: 0.001ms !important; transition-duration: 0.001ms !important; }
        }

        .showcase-label{
            width: 100%;
            margin: 0;
            padding: 0 clamp(16px, 3vw, 32px);
            display:flex;
            align-items:baseline;
            gap:14px;
        }
        .showcase-label .num{
            font-family:'IBM Plex Mono', monospace;
            font-size:13px;
            color: var(--accent);
            letter-spacing: 0.08em;
        }
        .showcase-label .name{
            font-family:'IBM Plex Mono', monospace;
            font-size:13px;
            color: var(--muted);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .showcase-label .line{
            flex:1;
            height:1px;
            background: var(--line);
        }
        .accent-topbar {
            height: 3px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--bsi-orange) 50%, var(--primary) 100%);
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 101;
        }
        .frame{
            width: 100%;
            max-width: none;
            min-height: 100vh;
            margin: 0;
            border: none;
            border-radius: 0;
            overflow-x: clip;
            background: var(--surface);
        }

        .stamp{ display:block; }
        .stamp .ring{ fill:none; stroke: var(--accent); }
        .stamp text{ font-family:'IBM Plex Mono', monospace; fill: var(--accent); }

        .landing-nav{
            width: 100%;
            height: 76px;
            padding: 0 clamp(20px, 4vw, 48px);
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(248, 250, 252, 0.92);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
        }
        .inner{
            width:100%;
            max-width: none;
            margin: 0;
        }
        .landing-nav .inner{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 18px;
        }
        .brand{ display:flex; align-items:center; gap:10px; }
        .brand-mark{
            position:relative;
            width: 140px;
            height: 42px;
            display:flex;
            align-items:center;
            justify-content:flex-start;
            flex-shrink:0;
        }
        .brand-mark img{
            display:block;
            width:100%;
            height:100%;
            object-fit:contain;
        }
        
        .brand-name{ font-family:'Plus Jakarta Sans', sans-serif; font-weight:700; font-size:18px; color: var(--primary-dark); }
        .brand-sub{ font-family:'Inter', sans-serif; font-size:13px; color:var(--ink-soft); letter-spacing:0.07em; font-weight:500; }
        .brand-note{ font-size:11.5px; color:var(--ink-soft); margin-top:3px; }
        .landing-nav nav{ display:flex; gap:36px; }
        .landing-nav nav a{
            font-size:14px;
            color: var(--ink-soft);
            padding-bottom: 4px;
            border-bottom: 2px solid transparent;
            transition: color .2s ease, border-color .2s ease;
        }
        .landing-nav nav a:hover{ color: var(--primary); }
        .landing-nav nav a.is-active{ color: var(--primary); border-bottom-color: var(--primary); }
        .nav-actions{ display:flex; gap:12px; align-items:center; }
        .nav-actions.guest-actions{ display:flex; }
        .nav-user{ display:none; position:relative; }
        .nav-user details{ position:relative; }
        .nav-user summary{
            list-style:none;
            display:flex;
            align-items:center;
            gap:8px;
            cursor:pointer;
            border:1px solid var(--line);
            border-radius: var(--radius);
            padding:7px 10px;
            background:#fff;
        }
        .nav-user summary::-webkit-details-marker{ display:none; }
        .nav-user .avatar{
            width:28px;
            height:28px;
            border-radius:50%;
            background: var(--primary-light);
            color: var(--primary-dark);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:11px;
            font-weight:700;
            font-family:'IBM Plex Mono', monospace;
        }
        .nav-user .name{ font-size:12.5px; color: var(--ink-soft); font-weight:600; }
        .nav-user .menu{
            position:absolute;
            right:0;
            top: calc(100% + 8px);
            background:#fff;
            border:1px solid var(--line);
            border-radius: var(--radius);
            min-width: 180px;
            padding:8px;
            display:flex;
            flex-direction:column;
            gap:4px;
            z-index:10;
        }
        .nav-user .menu a,
        .nav-user .menu button{
            width:100%;
            text-align:left;
            border:0;
            background:transparent;
            padding:8px 10px;
            border-radius:3px;
            font-size:13px;
            color: var(--ink-soft);
            cursor:pointer;
        }
        .nav-user .menu a:hover,
        .nav-user .menu button:hover{ background: var(--primary-light); color: var(--primary-dark); }
        body.logged-in .nav-actions.guest-actions{ display:none; }
        body.logged-in .nav-user{ display:block; }
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
            transition: transform .15s ease, background .15s ease, border-color .15s ease;
        }
        .btn:focus-visible{ outline: 2px solid var(--primary); outline-offset:2px; }
        .btn-ghost{ background:transparent; color: var(--primary-dark); border-color: var(--line); }
        .btn-ghost:hover{ border-color: var(--primary); }
        .btn-solid{ background: var(--primary); color:#fff; }
        .btn-solid:hover{ background: var(--primary-dark); transform: translateY(-1px); }
        .btn-block{ width:100%; }

        .hero{
            position:relative;
            width: 100%;
            height: calc(100vh - 76px);
            min-height: 500px;
            padding: clamp(20px, 3vw, 36px) clamp(20px, 4vw, 48px) 0;
            background: 
                radial-gradient(700px 350px at 90% 15%, rgba(0, 130, 123, 0.12), transparent 70%),
                radial-gradient(550px 300px at 75% 85%, rgba(232, 123, 36, 0.08), transparent 65%),
                radial-gradient(450px 220px at 15% 90%, rgba(232, 123, 36, 0.04), transparent 60%);
            display:flex;
            flex-direction: column;
            justify-content: space-between;

            overflow: hidden;
        }
        .hero-inner{
            flex: 1;
            display:grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(230px, 0.7fr);
            gap:40px;
            align-items:center;
        }
        .hero-eyebrow{
            display:inline-flex; align-items:center; gap:8px;
            font-family:'IBM Plex Mono', monospace;
            font-size:11px; letter-spacing:0.1em; text-transform:uppercase;
            color: var(--primary-dark);
            background: var(--primary-light);
            padding:6px 12px;
            border-radius: 20px;
            margin-bottom:22px;
            animation: rise .6s ease both;
        }
        .hero-eyebrow .dot{ width:6px; height:6px; border-radius:50%; background: var(--bsi-orange); box-shadow: 0 0 6px rgba(232, 123, 36, 0.5); }
        .hero h1{
            font-size: 52px;
            line-height: 1.08;
            font-weight: 600;
            max-width: 620px;
            animation: rise .7s .05s ease both;
        }
        .hero h1 em{ font-style: italic; color: var(--primary); font-weight:500; }
        .hero p.lede{
            margin-top:22px;
            font-size:17px;
            line-height:1.6;
            color: var(--ink);
            max-width: 480px;
            animation: rise .7s .1s ease both;
        }
        .hero-actions{ display:flex; gap:14px; margin-top:34px; animation: rise .7s .15s ease both; }
        .hero-note{ margin-top:20px; font-size:13px; color:var(--muted); animation: rise .7s .2s ease both; }
        .hero-note a{ color:var(--primary); font-weight:600; }
        @keyframes rise{ from{ opacity:0; transform: translateY(10px);} to{ opacity:1; transform:none; } }

        .hero-visual{ position:relative; display:flex; align-items:flex-start; justify-content:flex-end; padding-right: clamp(0px, 2vw, 24px); }
        .hero-visual .stamp-wrap{
            animation: rise .8s .2s ease both, tilt-in 1s .2s ease both;
            margin-top: 4px;
        }
        
        /* === Section divider dashed line with subtle gradient === */
        .section-divider{
            width:100%;
            height:1px;
            background: linear-gradient(90deg, transparent, rgba(232, 123, 36, 0.25) 30%, rgba(0, 130, 123, 0.25) 70%, transparent);
            margin:0;
        }

        .section{
            width:100%;
            min-height: 100vh;
            padding: clamp(56px, 7vw, 88px) clamp(20px, 4vw, 48px);
            display:flex;
            align-items:center;
            background: radial-gradient(650px 350px at 10% 50%, rgba(232, 123, 36, 0.04), transparent 70%);
        }
        /* Subtle background variations per section */
        .section.section-alt{
            background: 
                radial-gradient(500px 280px at 90% 20%, rgba(232, 123, 36, 0.06), transparent 65%),
                #F1F5F9;
        }
        .section-head{ max-width: 620px; margin-bottom: 52px; }
        .section-head .kicker{
            font-family:'IBM Plex Mono', monospace;
            font-size:12px; color:var(--accent); letter-spacing:0.1em; text-transform:uppercase;
            display:block; margin-bottom:14px;
        }
        .section-head h2{ font-size: 34px; font-weight:600; line-height:1.2; }
        .section-head h2 em{ font-style:italic; color:var(--primary); font-weight:500; }
        .section-head p{ margin-top:14px; color:var(--ink-soft); font-size:15.5px; line-height:1.6; }

        .flow{
            display:grid;
            grid-template-columns: repeat(6, 1fr);
            gap:0;
            position:relative;
        }
        .flow::before{
            content:"";
            position:absolute;
            top:22px; left:0; right:0;
            height:1px;
            background: repeating-linear-gradient(90deg, var(--accent) 0 6px, transparent 6px 12px);
            opacity: 0.4;
        }
        .flow-step{ position:relative; padding-right:14px; }
        .flow-step .idx{
            width:44px; height:44px;
            border-radius:50%;
            background: var(--surface);
            border:1.5px solid var(--primary);
            color: var(--primary);
            display:flex; align-items:center; justify-content:center;
            font-family:'IBM Plex Mono', monospace;
            font-size:13px;
            position:relative; z-index:1;
            margin-bottom:18px;
        }
        .flow-step h3{ font-size:14.5px; font-weight:600; margin-bottom:6px; }
        .flow-step p{ font-size:12.5px; color:var(--muted); line-height:1.5; }

        .modules{
            width:100%;
            min-height: 100vh;
            background: linear-gradient(180deg, #0A3F37 0%, #062823 100%);
            padding: clamp(56px, 7vw, 88px) clamp(20px, 4vw, 48px);
            color: #fff;
            display:flex;
            align-items:center;

        }
        .modules .section-head h2{ color:#fff; }
        .modules .section-head h2 em{ color: var(--accent); font-style:italic; font-weight:500; }
        .modules .section-head p{ color: rgba(255,255,255,0.68); }
        .modules .section-head .kicker{ color: var(--accent); }
        .module-grid{
            display:grid;
            grid-template-columns: repeat(2, 1fr);
            gap:16px;
        }
        .module-card{
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius);
            padding: 32px 28px 28px;
            position: relative;
            overflow: hidden;
            transition: background .2s ease, transform .2s ease;
        }
        .module-card:hover{ background: rgba(255, 255, 255, 0.09); transform: translateY(-2px); }
        .module-card .tag{
            font-family:'IBM Plex Mono', monospace;
            font-size:11px; color: var(--accent); letter-spacing:0.08em;
        }
        .module-card h3{ color:#fff; font-size:17px; margin:14px 0 8px; font-weight:600; }
        .module-card p{ font-size:13px; color: rgba(255,255,255,0.62); line-height:1.55; }
        /* Vertical dashed border between module cards */
        .module-card .card-perf{
            display: block;
            margin-top:20px;
            border-top: 1px dashed rgba(255,255,255,0.15);
            padding-top:14px;
            font-family:'IBM Plex Mono', monospace;
            font-size:10.5px;
            color: rgba(255,255,255,0.35);
            letter-spacing:0.07em;
        }

        /* === CTA Band — asymmetric layout === */
        .cta-band{
            width:100%;
            min-height: 100vh;
            padding: clamp(56px, 8vw, 90px) clamp(20px, 4vw, 48px);
            display:flex;
            align-items:center;
            background: 
                radial-gradient(600px 300px at 15% 20%, rgba(232, 123, 36, 0.08), transparent 60%),
                radial-gradient(500px 250px at 85% 80%, rgba(0, 130, 123, 0.08), transparent 60%),
                #F1F5F9;

            position: relative;
            overflow: hidden;
        }
        .cta-band::before{
            content:"";
            position:absolute;
            top:0; left:0; right:0;
            height:1px;
            background: repeating-linear-gradient(90deg, var(--line) 0 8px, transparent 8px 16px);
        }
        .cta-layout{
            display:grid;
            grid-template-columns: 1fr auto;
            gap:48px;
            align-items:center;
            width:100%;
        }
        .cta-content h2{ font-size:36px; max-width:520px; line-height:1.25; margin-bottom:16px; }
        .cta-content h2 em{ font-style:italic; color:var(--primary); font-weight:500; }
        .cta-content p{ color:var(--ink-soft); font-size:15.5px; margin-bottom:28px; max-width:440px; }
        .cta-row{ display:flex; gap:14px; }
        .cta-perks{ display:flex; gap:0; margin-top:24px; flex-wrap:wrap; }
        .cta-perk{ font-size:12px; color:var(--muted); padding:0 16px; border-right:1px solid var(--line); line-height:1; }
        .cta-perk:first-child{ padding-left:0; }
        .cta-perk:last-child{ border-right:none; }
        .cta-deco{ flex-shrink:0; opacity:0.18; }

        .landing-footer{
            width:100%;
            border-top:1px solid var(--line);
            padding: 28px clamp(20px, 4vw, 48px);
            font-size:12.5px; color:var(--muted);

        }
        .landing-footer .inner{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
        }

        .bantuan-grid{
            display:grid;
            grid-template-columns: 1.4fr 0.85fr;
            gap:48px;
            align-items:start;
        }
        .faq-list{ display:flex; flex-direction:column; }
        .faq-item{ border-bottom:1px solid var(--line); }
        .faq-item:first-child{ border-top:1px solid var(--line); }
        .faq-q{
            list-style:none;
            cursor:pointer;
            padding:15px 0;
            font-size:14px;
            font-weight:600;
            color:var(--ink);
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            user-select:none;
        }
        .faq-q::-webkit-details-marker{ display:none; }
        .faq-q::marker{ display:none; }
        .faq-q::after{ content:"+"; color:var(--primary); font-size:18px; font-weight:400; flex-shrink:0; transition:transform .2s ease; }
        details[open] > .faq-q::after{ content:"\2212"; }
        .faq-a{ padding:0 0 16px; font-size:13.5px; color:var(--ink-soft); line-height:1.65; margin:0; }
        .contact-card{
            background: #FFFFFF;
            border: 1px solid var(--line);
            border-top: 3px solid var(--bsi-orange);
            border-radius: var(--radius);
            padding:28px 24px;
            position:sticky;
            top:90px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }
        .contact-card .kicker{
            font-family:'IBM Plex Mono', monospace;
            font-size:11px; color:var(--bsi-orange); letter-spacing:0.1em; text-transform:uppercase;
            display:block; margin-bottom:12px;
        }
        .contact-card h3{ font-size:16px; font-weight:700; margin-bottom:22px; line-height:1.3; font-family:'Plus Jakarta Sans', sans-serif; }
        .contact-detail{ display:flex; flex-direction:column; gap:16px; }
        .contact-item label{ font-size:10.5px; color:var(--muted); font-family:'IBM Plex Mono',monospace; letter-spacing:0.08em; text-transform:uppercase; display:block; margin-bottom:4px; }
        .contact-item a{ font-size:13px; color:var(--primary); font-weight:500; line-height:1.5; display:block; word-break:break-all; }
        .contact-item span{ font-size:13px; color:var(--ink-soft); line-height:1.5; display:block; }

        @media (max-width: 1400px) and (min-width: 901px){
            .hero-inner{ grid-template-columns: minmax(0, 1.2fr) minmax(200px, 0.6fr); gap:32px; }
            .hero h1{ font-size: clamp(38px, 3.4vw, 52px); }
            .hero-visual{ padding-right: clamp(0px, 1vw, 12px); }
            .hero-visual .stamp-wrap svg{ width: 200px; height: 200px; }
        }

        @media (max-width: 900px){
            .hero{ padding:64px 24px; }
            .hero-inner{ grid-template-columns:1fr; }
            .hero h1{ font-size:clamp(30px, 5vw, 36px); }
            .hero-visual{ align-items:center; justify-content:center; padding-right:0; }
            .landing-nav nav{ display:none; }
            .flow{ grid-template-columns: 1fr 1fr; row-gap:32px; }
            .flow::before{ display:none; }
            .module-grid{ grid-template-columns: 1fr 1fr; }
            .section, .modules, .cta-band{ padding: 56px 24px; }
            .cta-layout{ grid-template-columns:1fr; }
            .cta-deco{ display:none; }
            .landing-nav .nav-actions{ gap:8px; }
            .bantuan-grid{ grid-template-columns:1fr; gap:32px; }
            .contact-card{ position:static; }
        }

        @media (max-width: 560px){
            .landing-nav{ justify-content:space-between; }
            .landing-nav .nav-actions{ width:100%; justify-content:flex-end; }
            .nav-actions .btn{ padding:9px 12px; font-size:12px; }
            .flow{ grid-template-columns:1fr; }
            .module-grid{ grid-template-columns:1fr; }
            .hero-actions, .cta-row{ flex-direction:column; }
            .hero-actions .btn, .cta-row .btn{ width:100%; }
            .landing-footer{ flex-direction:column; align-items:flex-start; }
        }

        .page-transition-root{ opacity:1; transform:none; }
        .page-transition-root *{ animation-delay:0s !important; }
        @media (prefers-reduced-motion: reduce){
            .page-transition-root{ opacity:1; transform:none; transition:none; }
        }
    </style>
</head>
<body class="{{ auth()->check() ? 'logged-in' : '' }}">
    @php
        $viewer = auth()->user();
        $viewerName = $viewer->name ?? 'Pengguna';
        $viewerInitials = collect(explode(' ', trim($viewerName)))->filter()->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('');
        $dashboardLink = $viewer && ($viewer->role ?? 'mahasiswa') === 'admin' ? route('admin.dashboard') : route('mahasiswa.dashboard');
    @endphp
    <div class="accent-topbar"></div>
    <div class="frame page-transition-root">
        <header class="landing-nav">
            <div class="inner">
                <div class="brand">
                    <div class="brand-mark" aria-label="Logo BSI" role="img">
                        <img src="{{ asset('images/logo_bsi.png') }}" alt="Logo BSI">
                    </div>
                    <div>
                        <div class="brand-name">SIMMM</div>
                        <div class="brand-sub">BSI AREA YOGYAKARTA</div>
                        <div class="brand-note">Prototype akademik, bukan aplikasi resmi BSI.</div>
                    </div>
                </div>
                <nav>
                    <a href="#hero" data-target="hero">Dashboard</a>
                    <a href="#alur-program" data-target="alur-program">Alur Program</a>
                    <a href="#modul-sistem" data-target="modul-sistem">Modul</a>
                    <a href="#tentang" data-target="tentang">Tentang</a>
                    <a href="#bantuan" data-target="bantuan">Bantuan</a>
                </nav>
                <div class="nav-actions guest-actions">
                    <a class="btn btn-ghost" href="{{ route('login') }}">Masuk</a>
                    <a class="btn btn-solid" href="{{ route('register') }}">Daftar Magang</a>
                </div>
                <div class="nav-user">
                    <details>
                        <summary>
                            <span class="avatar">{{ $viewerInitials ?: 'PG' }}</span>
                            <span class="name">{{ $viewerName }}</span>
                        </summary>
                        <div class="menu">
                            <a href="{{ $dashboardLink }}">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">Keluar</button>
                            </form>
                        </div>
                    </details>
                </div>
            </div>
        </header>

        <section class="hero" id="hero">
            <div class="inner hero-inner">
                <div>
                    <h1>Satu berkas, <em>satu pintu</em>, satu status.</h1>
                    <p class="lede">Sistem Informasi Manajemen Mahasiswa Magang atau SIMMM berguna untuk mempermudah proses magang di Bank Syariah Indonesia Area Yogyakarta. Mulai dari pendaftaran sampai penerbitan surat keterangan selesai magang yang nantinya menjadi satu alur yang dapat dipantau kapan saja dan oleh siapa saja yang berkepentingan.</p>
                    <div class="hero-actions">
                        <a class="btn btn-solid" href="{{ route('register') }}">Daftar Sekarang</a>
                    </div>
                    <p class="hero-note">Sudah Punya Akun? <a href="{{ route('login') }}">Masuk ke Akun</a></p>
                </div>
                <div class="hero-visual">
                    <div class="stamp-wrap">
                        <svg class="stamp" width="230" height="230" viewBox="0 0 230 230" overflow="visible">
                            <defs>
                                <!--
                                    r=96 → keliling ≈ 603px
                                    Teks 44 karakter × (8px font-size + ~2.1 letter-spacing) ≈ 545px → muat
                                    startOffset=12% → teks dimulai dari titik kiri-bawah lingkaran,
                                    sehingga jika ada gap, jatuh di sisi kiri-atas (tidak terlihat karena tertutup inner circle)
                                -->
                                <path id="circlePath" d="M 115,115 m -96,0 a 96,96 0 1,1 192,0 a 96,96 0 1,1 -192,0" />
                            </defs>
                            <circle class="ring" cx="115" cy="115" r="107" stroke-width="1.5"/>
                            <circle class="ring" cx="115" cy="115" r="80" stroke-width="1.5"/>
                            <text font-size="8.5" letter-spacing="2.1">
                                <textPath href="#circlePath" startOffset="12%">SISTEM INFORMASI MANAJEMEN MAHASISWA MAGANG</textPath>
                            </text>
                            <text x="115" y="106" text-anchor="middle" font-size="14" fill="var(--primary)" font-family="Plus Jakarta Sans, sans-serif" font-weight="700">TERVERIFIKASI</text>
                            <text x="115" y="125" text-anchor="middle" font-size="10" fill="var(--muted)" letter-spacing="1.5">BSI · AREA YOGYAKARTA</text>
                            <path d="M98 140 l11 11 l24 -24" stroke="var(--primary)" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>
        <section class="section" id="alur-program">
            <div class="inner">
                <div class="section-head">
                    <span class="kicker">Alur Program</span>
                    <h2>Enam Tahap Proses Pemagangan.</h2>
                </div>
                <div class="flow">
                    <div class="flow-step"><div class="idx">01</div><h3>Pengajuan Proposal</h3><p>Pengunggahan dokumen proposal magang dan surat pengantar dari kampus.</p></div>
                    <div class="flow-step"><div class="idx">02</div><h3>Proses Verifikasi</h3><p>Peninjauan berkas dan memutuskan langsung tanpa menunggu approval eksternal.</p></div>
                    <div class="flow-step"><div class="idx">03</div><h3>Penempatan</h3><p>Penetapan cabang/unit dan mentor pembimbing.</p></div>
                    <div class="flow-step"><div class="idx">04</div><h3>Pelaksanaan Magang</h3><p>Pengisian presensi dan logbook harian.</p></div>
                    <div class="flow-step"><div class="idx">05</div><h3>Penilaian</h3><p>Pemberian penilaian lapangan dari Admin/Mentor.</p></div>
                    <div class="flow-step"><div class="idx">06</div><h3>Kelulusan</h3><p>Penerbitan surat keterangan selesai magang secara otomatis setelah nilai lengkap.</p></div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>
        <section class="modules" id="modul-sistem">
            <div class="inner">
                <div class="section-head">
                    <span class="kicker">Modul Sistem</span>
                    <h2>Semua yang dibutuhkan tiap peran dalam <em>satu tempat</em>.</h2>
                </div>
                <div class="module-grid">
                    <div class="module-card">
                        <span class="tag">MAHASISWA</span>
                        <h3>Presensi &amp; Logbook</h3>
                        <p>Pencatatan kehadiran dan aktivitas harian langsung dari sistem, terekam setiap hari.</p>
                        <span class="card-perf"></span>
                    </div>
                    <div class="module-card">
                        <span class="tag">MAHASISWA</span>
                        <h3>Status &amp; Berkas</h3>
                        <p>Pemantauan status pendaftaran, peninjauan catatan revisi, dan pengelolaan dokumen yang telah diunggah.</p>
                        <span class="card-perf"></span>
                    </div>
                    <div class="module-card">
                        <span class="tag">ADMIN / MENTOR</span>
                        <h3>Verifikasi &amp; Penempatan</h3>
                        <p>Peninjauan berkas masuk, pemutusan diterima/revisi/ditolak, serta penetapan cabang/unit dan mentor pembimbing.</p>
                        <span class="card-perf"></span>
                    </div>
                    <div class="module-card">
                        <span class="tag">ADMIN / MENTOR</span>
                        <h3>Monitoring &amp; Penilaian</h3>
                        <p>Pemantauan logbook harian, rekapitulasi presensi, dan penilaian lapangan akhir secara terpadu.</p>
                        <span class="card-perf"></span>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>
        <section class="cta-band" id="tentang">
            <div class="inner">
                <div class="cta-layout">
                    <div class="cta-content">
                        <h2>Tertarik Mengikuti Magang di <em>BSI Area Yogyakarta</em>?</h2>
                        <p>Ajukan pendaftaran dalam waktu kurang dari 10 menit, lengkapi proposal dan surat pengantar, lalu tunggu konfirmasi.</p>
                        <div class="cta-row">
                            <a class="btn btn-solid" href="{{ route('register') }}">Daftar Sekarang</a>
                            <a class="btn btn-ghost" href="{{ route('login') }}">Masuk ke Akun</a>
                        </div>
                    </div>
        
                </div>
            </div>
        </section>

        <div class="section-divider"></div>
        <section class="section section-alt" id="bantuan">
            <div class="inner">
                <div class="section-head">
                    <span class="kicker">Bantuan</span>
                    <h2>Pertanyaan Umum (FAQ) dan Kontak Pengguna SIMMM.</h2>
                    <p>Pertanyaan yang sering ditanyakan seputar penggunaan sistem, proses pendaftaran, dan kegiatan magang.</p>
                </div>
                <div class="bantuan-grid">
                    <div class="faq-list">
                        <details class="faq-item">
                            <summary class="faq-q">Bagaimana cara mereset kata sandi akun??</summary>
                            <p class="faq-a">Klik tautan &ldquo;Lupa Password&rdquo; di halaman login, masukkan email yang terdaftar, lalu ikuti instruksi yang dikirimkan ke kotak masuk email kamu dalam 5&ndash;10 menit.</p>
                        </details>
                        <details class="faq-item">
                            <summary class="faq-q">Bagaimana jika status pendaftaran tidak berubah?</summary>
                            <p class="faq-a">Status biasanya diperbarui dalam 2 hari kerja setelah dokumen diterima lengkap. Jika lebih dari itu, segera hubungi admin melalui email resmi di panel sebelah kanan.</p>
                        </details>
                        <details class="faq-item">
                            <summary class="faq-q">Bagaimana jika lupa mengisi presensi harian?</summary>
                            <p class="faq-a">Presensi hanya bisa diisi pada hari yang bersangkutan. Jika terlewat karena alasan teknis, laporkan ke Admin/Mentor untuk pencatatan manual.</p>
                        </details>
                        <details class="faq-item">
                            <summary class="faq-q">Bagaimana cara menghubungi Admin/Mentor Pembimbing?</summary>
                            <p class="faq-a">Kontak Admin/Mentor tersedia di halaman Profil Magang setelah penempatan dikonfirmasi. Gunakan saluran komunikasi resmi yang tertera di sana.</p>
                        </details>
                        <details class="faq-item">
                            <summary class="faq-q">Bagaimana ketentuan format dokumen saat pendaftaran?</summary>
                            <p class="faq-a">Sistem menerima file PDF, JPG, dan PNG dengan ukuran maksimum 5 MB per file. Pastikan dokumen terbaca jelas sebelum diunggah.</p>
                        </details>
                    </div>
                    <div class="contact-card">
                        <span class="kicker">Kontak Langsung</span>
                        <h3>Admin Magang BSI Area Yogyakarta</h3>
                        <div class="contact-detail">
                            <div class="contact-item">
                                <label>Email Resmi</label>
                                <a href="mailto:magang.bsiareayogyakarta@bsi.co.id">magang.bsiareayogyakarta@bsi.co.id</a>
                            </div>
                            <div class="contact-item">
                                <label>Jam Layanan</label>
                                <span>Senin&ndash;Jumat, 08.00&ndash;16.00 WIB</span>
                            </div>
                            <div class="contact-item">
                                <label>Respons Email</label>
                                <span>Maksimum 7 hari kerja</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="landing-footer">
            <div class="inner">
                <span>© 2026 SIMMM — BSI Area Yogyakarta</span>
                <span class="mono">PROTOTIPE AKADEMIK - FARHAN ARVA AMANTA - UNIVERSITAS ANDALAS</span>
            </div>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navLinks = Array.from(document.querySelectorAll('.landing-nav nav a[data-target]'));
            const targets = ['hero', 'alur-program', 'modul-sistem', 'tentang', 'bantuan'];

            navLinks.forEach(function (link) {
                link.addEventListener('click', function (event) {
                    const targetId = link.getAttribute('data-target');
                    const targetEl = document.getElementById(targetId);
                    if (!targetEl) {
                        return;
                    }

                    event.preventDefault();
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    history.replaceState(null, '', '#' + targetId);
                });
            });

            const setActive = function (targetId) {
                navLinks.forEach(function (link) {
                    link.classList.toggle('is-active', link.getAttribute('data-target') === targetId);
                });
            };

            const observer = new IntersectionObserver(function (entries) {
                const visible = entries
                    .filter(function (entry) { return entry.isIntersecting; })
                    .sort(function (a, b) { return b.intersectionRatio - a.intersectionRatio; });

                if (visible.length > 0) {
                    setActive(visible[0].target.id);
                }
            }, {
                threshold: [0.45, 0.6],
                rootMargin: '-15% 0px -35% 0px'
            });

            targets.forEach(function (id) {
                const section = document.getElementById(id);
                if (section) {
                    observer.observe(section);
                }
            });

            const currentHash = window.location.hash.replace('#', '');
            if (currentHash && targets.includes(currentHash)) {
                setActive(currentHash);
            }
        });
    </script>
    <script src="{{ asset('js/simmm.js') }}"></script>
</body>
</html>