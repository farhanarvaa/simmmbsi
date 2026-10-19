<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMMM — Login</title>
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
        html, body{
            margin:0;
            padding:0;
            min-height:100vh;
            width:100%;
            background: var(--bg);
            color: var(--ink);
            font-family:'Inter', sans-serif;
            overflow-x:hidden;
        }
        h2,h3{ font-family:'Fraunces', serif; margin:0; }
        a{ color:inherit; text-decoration:none; }
        .login-shell{
            display:grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            min-height: 100vh;
            width: 100vw;
            max-width: 100vw;
        }
        .login-brand{
            background: var(--primary-dark);
            position:relative;
            padding: clamp(24px, 4vw, 56px);
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            overflow:hidden;
            color:#fff;
            min-height:100vh;
            width:100%;
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
        .brand-name{ font-family:'Fraunces', serif; font-weight:600; font-size:17px; }
        .brand-sub{ font-family:'IBM Plex Mono', monospace; font-size:10px; color:rgba(255,255,255,0.8); letter-spacing:0.08em; }
        .login-brand .stamp{ position:absolute; right:-40px; bottom:-40px; opacity:0.5; }
        .login-brand .stamp .ring{ fill:none; stroke: rgba(255,255,255,0.5); }
        .login-brand .stamp text{ fill: rgba(255,255,255,0.55); }
        .login-quote{ max-width:360px; position:relative; z-index:1; }
        .login-quote h3{ font-size:clamp(26px, 2.5vw, 42px); font-weight:500; line-height:1.35; color:#fff; }
        .login-quote p{ margin-top:16px; font-size:13.5px; color: rgba(255,255,255,0.6); }
        .login-foot-note{ font-family:'IBM Plex Mono', monospace; font-size:11px; color: rgba(255,255,255,0.45); position:relative; z-index:1; }

        .login-form-wrap{
            padding: clamp(28px, 4vw, 56px);
            display:flex;
            flex-direction:column;
            justify-content:center;
            max-width: 520px;
            margin:0 auto;
            width:100%;
        }
        .login-form-wrap h2{ font-size: clamp(28px, 2vw, 42px); font-weight:600; margin-bottom:8px; }
        .login-form-wrap > p.sub{ color: var(--muted); font-size:14px; margin-bottom:30px; }

        .field{ margin-bottom:18px; }
        .field label{
            display:block;
            font-size:12.5px;
            font-weight:600;
            color: var(--ink-soft);
            margin-bottom:7px;
        }
        .field input{
            width:100%;
            padding: 12px 14px;
            border:1px solid var(--line);
            border-radius: var(--radius);
            font-family:'Inter', sans-serif;
            font-size:14.5px;
            background: var(--surface);
            color: var(--ink);
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .field input:focus{
            outline:none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        .field input::placeholder{ color: #A9B2AE; }
        .field .field-hint{ font-size:11.5px; color:var(--muted); margin-top:5px; }
        .password-field{ position:relative; }
        .password-field input{ padding-right:44px; }
        .password-field input::-ms-reveal,
        .password-field input::-ms-clear{ display:none; }
        .password-toggle{ position:absolute; right:8px; bottom:7px; width:32px; height:32px; border:0; background:transparent; color:var(--muted); cursor:pointer; display:grid; place-items:center; border-radius:3px; }
        .password-toggle:hover,.password-toggle:focus-visible{ color:var(--primary); background:var(--primary-light); outline:none; }
        .password-toggle svg{ width:18px; height:18px; }
        .password-toggle .eye-off{ display:none; }
        .password-toggle.is-visible .eye{ display:none; }
        .password-toggle.is-visible .eye-off{ display:block; }

        .form-row{ display:flex; justify-content:space-between; align-items:center; margin: 4px 0 26px; font-size:13px; }
        .form-row label{ display:flex; align-items:center; gap:7px; color: var(--ink-soft); }
        .form-row .help-text{ color: var(--muted); font-size:12px; font-style:italic; }

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
            text-decoration:none;
        }
        .btn-solid{ background: var(--primary); color:#fff; }
        .btn-solid:hover{ background: var(--primary-dark); transform: translateY(-1px); }
        .btn-block{ width:100%; }

        .login-help{
            margin-top:28px;
            font-size:13px;
            color: var(--muted);
            text-align:center;
        }
        .login-help a{ color: var(--primary); font-weight:600; }
        .page-transition-root{ opacity:1; transform:none; }

        @media (max-width: 900px){
            .login-shell{ grid-template-columns:1fr; min-height:auto; }
            .login-brand{ display:none; }
            .login-form-wrap{ padding: 40px 24px; max-width: 100%; }
        }

        @media (max-width: 480px){
            .login-form-wrap h2{ font-size: 28px; }
            .form-row{ flex-direction:column; align-items:flex-start; gap:12px; }
        }
        @media (min-width: 901px) and (max-height: 800px){
            html, body{ height:100%; overflow:hidden; }
            .login-shell{ height:100vh; min-height:0; }
            .login-brand{ min-height:0; padding:28px 4vw; }
            .login-form-wrap{ padding:24px 4vw; max-width:560px; }
            .login-form-wrap h2{ font-size:32px; margin-bottom:4px; }
            .login-form-wrap > p.sub{ margin-bottom:18px; }
            .field{ margin-bottom:12px; }
            .field label{ margin-bottom:4px; }
            .field input{ padding:9px 12px; }
            .password-toggle{ bottom:3px; }
            .form-row{ margin:4px 0 16px; }
            .login-help{ margin-top:16px; }
        }
        @media (prefers-reduced-motion: reduce){
            .page-transition-root{ opacity:1; transform:none; transition:none; }
        }
    </style>
</head>
<body>
    <div class="login-shell page-transition-root">
        <div class="login-brand">
            <div class="brand">
                <div class="brand-mark" aria-label="Logo BSI" role="img">
                    <img src="{{ asset('images/logo_bsi.png') }}" alt="Logo BSI">
                </div>
                <div>
                    <div class="brand-name">SIMMM</div>
                    <div class="brand-sub">BSI AREA YOGYAKARTA</div>
                </div>
            </div>
            <div class="login-quote">
                <h3>Tidak ada lagi laporan yang tercecer. Semua tercatat di satu tempat sejak hari pertama.</h3>
            </div>
            <div class="login-foot-note"></div>
        </div>

        <div class="login-form-wrap">
            <h2>Selamat Datang Kembali</h2>
            <p class="sub">Masuk untuk melanjutkan aktivitas magangmu.</p>

            @if ($errors->any())
                <div style="margin-bottom: 18px; background:#fff4f4; border:1px solid #f2caca; color:#8f2d2d; border-radius:3px; padding:10px 12px; font-size:13px;">
                    <ul style="margin:0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field">
                    <label for="login_id">Email atau NIM</label>
                    <input id="login_id" type="text" name="login_id" value="{{ old('login_id') }}" placeholder="nama@kampus.ac.id atau NIM" required autocomplete="username">
                    <div class="field-hint">Mahasiswa dapat login menggunakan alamat email atau NIM yang terdaftar.</div>
                </div>
                <div class="field">
                    <label for="password">Kata Sandi</label>
                    <div class="password-field">
                        <input id="password" type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Tampilkan kata sandi" aria-pressed="false">
                            <svg class="eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 3 18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6.5 0 10 7 10 7a18.3 18.3 0 0 1-3 3.8"/><path d="M6.6 6.6C3.7 8.5 2 12 2 12s3.5 7 10 7a10.6 10.6 0 0 0 4.1-.8"/></svg>
                        </button>
                    </div>
                </div>

                <div class="form-row">
                    <label><input type="checkbox" name="remember"> Ingat Saya</label>
                    <span class="help-text">Hubungi Admin jika lupa kata sandi</span>
                </div>

                <button type="submit" class="btn btn-solid btn-block">Masuk</button>
            </form>

            <p class="login-help">Belum Punya Akun? <a href="{{ route('register') }}">Daftar Magang Disini</a></p>
        </div>
    </div>
    <script src="{{ asset('js/simmm.js') }}"></script>
</body>
</html>
