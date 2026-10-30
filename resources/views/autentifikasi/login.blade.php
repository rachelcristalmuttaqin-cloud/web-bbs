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
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --accent-teal: #14b8a6;
            --accent-indigo: #6366f1;
            --bg-dark: #070d18;
            --card-bg: rgba(15, 23, 42, 0.75);
            --card-border: rgba(255, 255, 255, 0.1);
            --card-border-hover: rgba(56, 189, 248, 0.35);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --input-bg: rgba(255, 255, 255, 0.04);
            --input-border: rgba(255, 255, 255, 0.12);
            --input-focus-border: #38bdf8;
            --glow-color: rgba(14, 165, 233, 0.18);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-dark);
            color: var(--text-main);
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(20, 184, 166, 0.15), transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(99, 102, 241, 0.16), transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(14, 165, 233, 0.1), transparent 60%);
        }

        /* Subtle Geometric Background Grid */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse at center, rgba(0, 0, 0, 1) 30%, transparent 80%);
            z-index: 0;
        }

        /* Ambient Animated Glow Orbs */
        .ambient-glow {
            position: fixed;
            width: 550px;
            height: 550px;
            border-radius: 50%;
            filter: blur(140px);
            opacity: 0.5;
            pointer-events: none;
            z-index: 0;
            animation: floatGlow 14s ease-in-out infinite alternate;
        }

        .ambient-glow-1 {
            top: -120px;
            left: -120px;
            background: linear-gradient(135deg, #14b8a6, #0284c7);
        }

        .ambient-glow-2 {
            bottom: -140px;
            right: -140px;
            background: linear-gradient(135deg, #6366f1, #0ea5e9);
            animation-delay: -7s;
        }

        @keyframes floatGlow {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(40px, 30px) scale(1.08); }
            100% { transform: translate(-30px, 50px) scale(0.95); }
        }

        /* Glassmorphic Login Card */
        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 28px;
            padding: 2.75rem 2.25rem;
            box-shadow: 
                0 30px 60px -15px rgba(0, 0, 0, 0.6),
                0 0 0 1px rgba(255, 255, 255, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            animation: cardEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        /* Top Accent Highlight Bar */
        .login-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #14b8a6, #38bdf8, #6366f1, #14b8a6);
            background-size: 300% 100%;
            animation: shimmerLine 8s ease infinite;
        }

        @keyframes shimmerLine {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Brand & Header Section */
        .brand-header {
            text-align: center;
            margin-bottom: 2.25rem;
        }

        .brand-icon-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 1.25rem;
        }

        .brand-icon-glow {
            position: absolute;
            inset: -4px;
            background: linear-gradient(135deg, #14b8a6, #38bdf8, #6366f1);
            border-radius: 22px;
            filter: blur(12px);
            opacity: 0.6;
            animation: pulseHalo 4s ease-in-out infinite;
        }

        @keyframes pulseHalo {
            0%, 100% { opacity: 0.45; transform: scale(0.96); }
            50% { opacity: 0.85; transform: scale(1.05); }
        }

        .brand-icon {
            position: relative;
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, rgba(20, 184, 166, 0.9), rgba(2, 132, 199, 0.95) 55%, rgba(79, 70, 229, 0.9));
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(2, 132, 199, 0.35);
        }

        .brand-icon svg {
            width: 32px;
            height: 32px;
            color: #ffffff;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.25));
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.75rem;
            background: rgba(56, 189, 248, 0.1);
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #38bdf8;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }

        .brand-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #38bdf8;
            box-shadow: 0 0 8px #38bdf8;
            animation: dotBlink 2s ease infinite;
        }

        @keyframes dotBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .brand-title {
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #ffffff;
            margin-bottom: 0.4rem;
            line-height: 1.25;
        }

        .brand-title .gradient-text {
            background: linear-gradient(135deg, #ffffff 40%, #7dd3fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.5;
            font-weight: 400;
        }

        /* Alert Notifications */
        .alert {
            padding: 0.875rem 1.15rem;
            border-radius: 14px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            line-height: 1.45;
            animation: fadeInAlert 0.3s ease;
        }

        @keyframes fadeInAlert {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.28);
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.28);
            color: #86efac;
        }

        .alert svg {
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.35rem;
        }

        .form-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.825rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 0.55rem;
            letter-spacing: 0.01em;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1.05rem;
            width: 20px;
            height: 20px;
            color: #64748b;
            pointer-events: none;
            transition: color 0.25s ease, transform 0.25s ease;
            z-index: 2;
        }

        .form-input {
            width: 100%;
            height: 52px;
            padding: 0 1rem 0 3rem;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 14px;
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 400;
            outline: none;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .form-input.has-toggle {
            padding-right: 3rem;
        }

        .form-input::placeholder {
            color: #64748b;
            font-size: 0.9rem;
        }

        .form-input:hover {
            border-color: rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.06);
        }

        .form-input:focus {
            border-color: var(--input-focus-border);
            background: rgba(14, 165, 233, 0.05);
            box-shadow: 0 0 0 4px var(--glow-color);
        }

        .input-wrapper:focus-within .input-icon {
            color: var(--primary);
            transform: scale(1.08);
        }

        /* Password Visibility Toggle Button */
        .btn-toggle-pw {
            position: absolute;
            right: 0.85rem;
            background: transparent;
            border: none;
            color: #64748b;
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
            color: #e2e8f0;
            background: rgba(255, 255, 255, 0.08);
        }

        .btn-toggle-pw svg {
            width: 19px;
            height: 19px;
        }

        /* Remember Me Row */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.25rem;
            margin-bottom: 1.6rem;
            font-size: 0.85rem;
        }

        .checkbox-label {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            color: #94a3b8;
            cursor: pointer;
            font-weight: 500;
            user-select: none;
            transition: color 0.2s ease;
        }

        .checkbox-label:hover {
            color: #cbd5e1;
        }

        .checkbox-custom {
            position: relative;
            width: 18px;
            height: 18px;
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.04);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkbox-input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .checkbox-input:checked + .checkbox-custom {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            border-color: #38bdf8;
            box-shadow: 0 0 10px rgba(14, 165, 233, 0.4);
        }

        .checkbox-custom svg {
            width: 12px;
            height: 12px;
            color: #ffffff;
            opacity: 0;
            transform: scale(0.6);
            transition: all 0.2s ease;
        }

        .checkbox-input:checked + .checkbox-custom svg {
            opacity: 1;
            transform: scale(1);
        }

        /* Submit Button */
        .btn-submit {
            position: relative;
            width: 100%;
            height: 52px;
            background: linear-gradient(135deg, #0f766e, #0284c7 55%, #3b82f6);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            color: #ffffff;
            font-size: 0.98rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            box-shadow: 
                0 12px 24px -6px rgba(2, 132, 199, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.25);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .btn-submit::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 70%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: skewX(-20deg);
            transition: left 0.75s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 16px 30px -6px rgba(2, 132, 199, 0.55),
                0 0 20px rgba(56, 189, 248, 0.3);
            background: linear-gradient(135deg, #0d6b64, #0369a1 55%, #2563eb);
        }

        .btn-submit:hover::before {
            left: 150%;
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 6px 14px -3px rgba(2, 132, 199, 0.4);
        }

        .btn-submit svg {
            width: 18px;
            height: 18px;
            transition: transform 0.2s ease;
        }

        .btn-submit:hover svg {
            transform: translateX(3px);
        }

        /* Footer Registration Link */
        .footer-text {
            text-align: center;
            margin-top: 1.85rem;
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .footer-text a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .footer-text a::after {
            content: "";
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1.5px;
            background: #38bdf8;
            transition: width 0.25s ease;
        }

        .footer-text a:hover {
            color: #7dd3fc;
        }

        .footer-text a:hover::after {
            width: 100%;
        }

        /* Security Micro Badge */
        .security-badge {
            margin-top: 1.75rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.72rem;
            color: #64748b;
            letter-spacing: 0.02em;
        }

        .security-badge svg {
            width: 14px;
            height: 14px;
            color: #14b8a6;
        }

        /* Responsive Mobile Adjustment */
        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }

            .login-card {
                padding: 2.25rem 1.45rem;
                border-radius: 24px;
            }

            .brand-title {
                font-size: 1.45rem;
            }

            .form-input {
                font-size: 16px; /* Prevents auto-zoom in mobile Safari/Chrome */
            }
        }
    </style>
</head>
<body>
    <!-- Ambient Background Glows -->
    <div class="ambient-glow ambient-glow-1"></div>
    <div class="ambient-glow ambient-glow-2"></div>

    <div class="login-card">
        <!-- Brand Header Section -->
        <div class="brand-header">
            <div class="brand-icon-wrapper">
                <div class="brand-icon-glow"></div>
                <div class="brand-icon">
                    <!-- Safety Shield with Checkmark Icon -->
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

            <h1 class="brand-title">
                <span class="gradient-text">Selamat Datang</span>
            </h1>
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

        <!-- Error Alert (Flash) -->
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
                    <!-- Show/Hide Password Toggle -->
                    <button type="button" class="btn-toggle-pw" id="togglePassword" aria-label="Tampilkan atau sembunyikan password">
                        <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="remember-row">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" class="checkbox-input" id="remember">
                    <span class="checkbox-custom">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </span>
                    <span>Ingat Saya</span>
                </label>
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

        <!-- Footer Registration Link -->
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

    <!-- Interactive Script: Toggle Password Visibility -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (toggleBtn && passwordInput && eyeIcon) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

                    // Toggle eye SVG path
                    if (isPassword) {
                        // Eye-off icon
                        eyeIcon.innerHTML = `
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        `;
                        toggleBtn.style.color = '#38bdf8';
                    } else {
                        // Regular eye icon
                        eyeIcon.innerHTML = `
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        `;
                        toggleBtn.style.color = '#64748b';
                    }
                });
            }
        });
    </script>
</body>
</html>
