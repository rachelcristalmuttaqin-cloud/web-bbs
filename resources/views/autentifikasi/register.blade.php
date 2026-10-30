<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun - Sistem Observasi BBS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0284c7;
            --primary-hover: #0369a1;
            --accent-teal: #0d9488;
            --bg-page: #f8fafc;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
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

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-page);
            color: var(--text-heading);
            padding: 2rem 1.5rem;
            position: relative;
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(13, 148, 136, 0.08) 0px, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(2, 132, 199, 0.08) 0px, transparent 45%),
                radial-gradient(circle at 50% 50%, rgba(99, 102, 241, 0.04) 0px, transparent 60%);
        }

        /* Subtle Geometric Background Grid */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image: 
                linear-gradient(rgba(15, 23, 42, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, 0.03) 1px, transparent 1px);
            background-size: 36px 36px;
            mask-image: radial-gradient(ellipse at center, rgba(0, 0, 0, 1) 40%, transparent 85%);
            z-index: 0;
        }

        .ambient-glow {
            position: fixed;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            filter: blur(130px);
            opacity: 0.65;
            pointer-events: none;
            z-index: 0;
        }

        .ambient-glow-1 {
            top: -100px;
            left: -100px;
            background: rgba(13, 148, 136, 0.12);
        }

        .ambient-glow-2 {
            bottom: -120px;
            right: -120px;
            background: rgba(2, 132, 199, 0.12);
        }

        .register-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 620px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.5rem 2.25rem;
            box-shadow: 
                0 20px 40px -15px rgba(15, 23, 42, 0.07),
                0 0 1px 1px rgba(15, 23, 42, 0.02);
            animation: cardEntrance 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            margin: auto;
        }

        .register-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0d9488, #0284c7 50%, #4f46e5);
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
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #0d9488, #0284c7 60%, #2563eb);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 22px -4px rgba(2, 132, 199, 0.35);
        }

        .brand-icon svg {
            width: 28px;
            height: 28px;
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
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert svg {
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.1rem 1rem;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-label {
            display: block;
            font-size: 0.825rem;
            font-weight: 600;
            color: var(--text-body);
            margin-bottom: 0.45rem;
        }

        /* White, Clear, Readable Input Box */
        .form-input {
            width: 100%;
            height: 48px;
            padding: 0 1rem;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 12px;
            color: var(--text-heading);
            font-size: 0.92rem;
            font-weight: 500;
            outline: none;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
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

        /* Role Selector Cards */
        .role-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem;
        }

        .role-option {
            cursor: pointer;
            position: relative;
        }

        .role-option input {
            position: absolute;
            opacity: 0;
        }

        .role-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.85rem 1rem;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            text-align: center;
            transition: all 0.2s ease;
            min-height: 72px;
        }

        .role-card span {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-heading);
        }

        .role-card small {
            font-size: 0.725rem;
            color: var(--text-muted);
            margin-top: 0.2rem;
        }

        .role-option input:checked + .role-card {
            border-color: var(--primary);
            background: #eff6ff;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .role-option input:checked + .role-card span {
            color: var(--primary);
        }

        /* Submit Button */
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
            box-shadow: 0 10px 20px -5px rgba(2, 132, 199, 0.4);
            transition: all 0.2s ease;
            margin-top: 1.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 24px -5px rgba(2, 132, 199, 0.5);
            background: linear-gradient(135deg, #0b7f74, #0369a1 60%, #1d4ed8);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Footer Link */
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

        @media (max-width: 600px) {
            body { padding: 1rem; }
            .register-card { padding: 2rem 1.25rem; border-radius: 20px; }
            .form-grid { grid-template-columns: 1fr; gap: 0.85rem; }
            .form-group.full-width { grid-column: auto; }
            .brand-title { font-size: 1.4rem; }
            .form-input { font-size: 16px; }
        }
    </style>
</head>
<body>
    <div class="ambient-glow ambient-glow-1"></div>
    <div class="ambient-glow ambient-glow-2"></div>

    <div class="register-card">
        <!-- Brand Header Section -->
        <div class="brand-header">
            <div class="brand-icon-wrapper">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                </div>
            </div>

            <div class="brand-badge">
                <span class="dot"></span>
                <span>Behavior-Based Safety</span>
            </div>

            <h1 class="brand-title">Pendaftaran Akun BBS</h1>
            <p class="brand-subtitle">Daftarkan akun karyawan atau permohonan Admin HES</p>
        </div>

        @if($errors->any())
            <div class="alert">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required autofocus>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="nama@perusahaan.com" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="no_hp" class="form-label">No. Telepon / HP</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-input" placeholder="081234567890" value="{{ old('no_hp') }}" required>
                </div>

                <div class="form-group">
                    <label for="jabatan" class="form-label">Jabatan / Posisi</label>
                    <input type="text" id="jabatan" name="jabatan" class="form-input" placeholder="Contoh: Teknisi Lapangan" value="{{ old('jabatan') }}" required>
                </div>

                <div class="form-group">
                    <label for="unit_departemen" class="form-label">Unit / Departemen</label>
                    <input type="text" id="unit_departemen" name="unit_departemen" class="form-input" placeholder="Contoh: Operasional Plant A" value="{{ old('unit_departemen') }}" required>
                </div>

                <div class="form-group full-width">
                    <label for="no_batch" class="form-label">No. Badge <span style="font-weight:400; color:#94a3b8;">(Opsional)</span></label>
                    <input type="text" id="no_batch" name="no_batch" class="form-input" placeholder="Masukkan nomor badge" value="{{ old('no_batch') }}">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Jenis Akun</label>
                    <div class="role-selector">
                        <label class="role-option">
                            <input type="radio" name="role" value="karyawan" {{ old('role', 'karyawan') === 'karyawan' ? 'checked' : '' }} required>
                            <span class="role-card"><span>Karyawan</span><small>Akun pelapor observasi BBS</small></span>
                        </label>
                        <label class="role-option">
                            <input type="radio" name="role" value="admin" {{ old('role') === 'admin' ? 'checked' : '' }} required>
                            <span class="role-card"><span>Calon Admin HES</span><small>Aktif setelah disetujui Admin</small></span>
                        </label>
                    </div>
                    <small style="display:block;color:#64748b;margin-top:.45rem;line-height:1.5;font-size:0.75rem;">Semua pendaftaran akun akan ditinjau Admin HES sebelum dapat digunakan.</small>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Minimal 8 karakter" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Ulangi password" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <span>Kirim Pendaftaran Akun</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </form>

        <p class="footer-text">
            Sudah memiliki akun? <a href="{{ route('login') }}">Masuk di sini &rarr;</a>
        </p>
    </div>
</body>
</html>
