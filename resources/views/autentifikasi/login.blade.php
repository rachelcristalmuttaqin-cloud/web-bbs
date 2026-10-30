<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Observasi BBS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0284c7;
            --primary-hover: #0369a1;
            --accent-teal: #0d9488;
            --accent-blue: #0ea5e9;
            --bg-ocean: #0e2439;
            --card-bg: #ffffff;
            --card-border: rgba(255, 255, 255, 0.85);
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --input-bg: #ffffff;
            --input-border: #cbd5e1;
            --input-focus-border: #0284c7;
            --input-focus-ring: rgba(2, 132, 199, 0.16);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* Background: Biru tenang yang pas (tidak terlalu kuat, tidak terlalu lembut) */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-ocean);
            background-image: 
                radial-gradient(ellipse at 15% 15%, rgba(13, 148, 136, 0.32) 0%, transparent 50%),
                radial-gradient(ellipse at 85% 85%, rgba(2, 132, 199, 0.32) 0%, transparent 55%),
                radial-gradient(circle at 50% 50%, rgba(14, 165, 233, 0.16) 0%, transparent 65%);
            color: var(--text-heading);
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Grid Halus Geometris */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
            background-size: 38px 38px;
            mask-image: radial-gradient(ellipse at center, rgba(0, 0, 0, 1) 35%, transparent 85%);
            z-index: 0;
        }

        /* Pendaran Cahaya Dinamis (Ambient Glow) Toska & Biru Tenang */
        .ambient-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(125px);
            pointer-events: none;
            z-index: 0;
            opacity: 0.7;
            animation: floatGlow 14s ease-in-out infinite alternate;
        }

        .ambient-glow-teal {
            width: 480px;
            height: 480px;
            top: -80px;
            left: -80px;
            background: radial-gradient(circle, rgba(13, 148, 136, 0.45), rgba(2, 132, 199, 0.25));
        }

        .ambient-glow-blue {
            width: 520px;
            height: 520px;
            bottom: -100px;
            right: -100px;
            background: radial-gradient(circle, rgba(2, 132, 199, 0.45), rgba(13, 148, 136, 0.25));
            animation-delay: -7s;
        }

        @keyframes floatGlow {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(35px, 25px) scale(1.08); }
            100% { transform: translate(-25px, 35px) scale(0.95); }
        }

        /* Kartu Login Putih Elegan */
        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.5rem 2.25rem;
            box-shadow: 
                0 25px 55px -12px rgba(7, 18, 30, 0.48),
                0 0 0 1px rgba(255, 255, 255, 0.15);
            animation: cardEntrance 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        /* Garis Aksen Gradasi Toska & Biru di Atas Kartu */
        .login-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0d9488, #0284c7 50%, #3b82f6);
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Brand & Header Section */
        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #0d9488, #0284c7 60%, #2563eb);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 22px -4px rgba(2, 132, 199, 0.4);
        }

        .brand-icon svg {
            width: 30px;
            height: 30px;
            color: #ffffff;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.75rem;
            background: #e0f2fe;
            border: 1px solid #bae6fd;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #0369a1;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }

        .brand-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #0284c7;
        }

        .brand-title {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text-heading);
            margin-bottom: 0.35rem;
        }

        .brand-subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* Alert Notifications */
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 1.35rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            line-height: 1.45;
            font-weight: 500;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .alert svg {
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* Form Group */
        .form-group {
            margin-bottom: 1.35rem;
        }

        .form-label {
            display: block;
            font-size: 0.825rem;
            font-weight: 600;
            color: var(--text-body);
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            width: 19px;
            height: 19px;
            color: #94a3b8;
            pointer-events: none;
            transition: color 0.2s ease;
            z-index: 2;
        }

        /* Text Box PUTIH Bersih & Jelas Terbaca */
        .form-input {
            width: 100%;
            height: 50px;
            padding: 0 1rem 0 2.85rem;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 12px;
            color: var(--text-heading);
            font-size: 0.95rem;
            font-weight: 500;
            outline: none;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
        }

        .form-input.has-toggle {
            padding-right: 3rem;
        }

        .form-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .form-input:hover {
            border-color: #94a3b8;
        }

        .form-input:focus {
            border-color: var(--input-focus-border);
            box-shadow: 0 0 0 4px var(--input-focus-ring);
        }

        .input-wrapper:focus-within .input-icon {
            color: var(--primary);
        }

        /* Tombol Toggle Lihat Sandi */
        .btn-toggle-pw {
            position: absolute;
            right: 0.75rem;
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.4rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s, background-color 0.2s;
            z-index: 2;
        }

        .btn-toggle-pw:hover {
            color: #334155;
            background: #f1f5f9;
        }

        .btn-toggle-pw svg {
            width: 19px;
            height: 19px;
        }

        /* Ingat Saya & Lupa Password */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.25rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }

        .forgot-pw-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.825rem;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .forgot-pw-link:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .checkbox-label {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            color: var(--text-muted);
            cursor: pointer;
            font-weight: 500;
            user-select: none;
        }

        .checkbox-label:hover {
            color: var(--text-body);
        }

        .checkbox-label input {
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        /* Tombol Masuk Gradasi Toska & Biru */
        .btn-submit {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #0d9488, #0284c7 60%, #2563eb);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 0.98rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 10px 22px -5px rgba(2, 132, 199, 0.45);
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 26px -5px rgba(2, 132, 199, 0.55);
            background: linear-gradient(135deg, #0b7f74, #0369a1 60%, #1d4ed8);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit svg {
            width: 18px;
            height: 18px;
            transition: transform 0.2s ease;
        }

        .btn-submit:hover svg {
            transform: translateX(3px);
        }

        /* Link Pendaftaran */
        .footer-text {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .footer-text a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .footer-text a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        /* Lencana Keamanan */
        .security-badge {
            margin-top: 1.5rem;
            padding-top: 1.15rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            font-size: 0.72rem;
            color: #94a3b8;
        }

        .security-badge svg {
            width: 14px;
            height: 14px;
            color: #0d9488;
        }

        @media (max-width: 480px) {
            body { padding: 1rem; }
            .login-card { padding: 2rem 1.35rem; border-radius: 20px; }
            .brand-title { font-size: 1.4rem; }
            .form-input { font-size: 16px; }
        }
    </style>
</head>
<body>
    <!-- Ambient Glow Orbs: Pendaran Toska & Biru Tenang -->
    <div class="ambient-glow ambient-glow-teal"></div>
    <div class="ambient-glow ambient-glow-blue"></div>

    <div class="login-card">
        <!-- Brand Header Section -->
        <div class="brand-header">
            <div class="brand-icon-wrapper">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <polyline points="9 12 11 14 15 10"/>
                    </svg>
                </div>
            </div>

            <div class="brand-badge">
                <span class="dot"></span>
                <span>Behavior-Based Safety</span>
            </div>

            <h1 class="brand-title">Selamat Datang</h1>
            <p class="brand-subtitle">Silakan masuk ke Portal Observasi Keselamatan BBS</p>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Error Alert -->
        @if(session('error'))
            <div class="alert alert-error">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="alert alert-error">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <!-- Email Field -->
            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <div class="input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="nama@perusahaan.com" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        autocomplete="email"
                    >
                </div>
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input has-toggle" 
                        placeholder="••••••••" 
                        required
                        autocomplete="current-password"
                    >
                    <button type="button" class="btn-toggle-pw" id="togglePassword" aria-label="Tampilkan atau sembunyikan password">
                        <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="remember-row">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Ingat Saya</span>
                </label>
                <a href="{{ route('password.request') }}" class="forgot-pw-link">Lupa Password?</a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">
                <span>Masuk ke Akun</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </form>

        <!-- Footer Link -->
        <p class="footer-text">
            Belum memiliki akun? <a href="{{ route('register') }}">Daftar di sini &rarr;</a>
        </p>

        <!-- Security Badge -->
        <div class="security-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <span>Sistem Keamanan & Observasi Terenkripsi</span>
        </div>
    </div>

    <!-- Toggle Password Visibility Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (toggleBtn && passwordInput && eyeIcon) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

                    if (isPassword) {
                        eyeIcon.innerHTML = `
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        `;
                        toggleBtn.style.color = '#0284c7';
                    } else {
                        eyeIcon.innerHTML = `
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        `;
                        toggleBtn.style.color = '#94a3b8';
                    }
                });
            }
        });
    </script>
</body>
</html>
