<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Sistem BBS</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 24px; color: #1e293b;">
    <div style="max-width: 560px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 24px;">
            <h2 style="color: #0284c7; margin: 0 0 8px 0;">Sistem Observasi BBS</h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Behavior-Based Safety & Risk Intelligence</p>
        </div>

        <p>Halo, <strong>{{ $user->name }}</strong>,</p>
        <p>Kami menerima permohonan untuk mereset kata sandi akun Anda di Sistem Observasi BBS.</p>
        <p>Silakan klik tombol di bawah ini untuk memperbarui kata sandi Anda:</p>

        <div style="text-align: center; margin: 28px 0;">
            <a href="{{ $resetUrl }}" style="background: linear-gradient(135deg, #0d9488, #0284c7); color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; font-size: 15px; display: inline-block;">Reset Password Akun</a>
        </div>

        <p style="font-size: 13px; color: #64748b; line-height: 1.5;">
            Tautan ini berlaku selama <strong>60 menit</strong>. Jika Anda tidak pernah meminta pengaturan ulang kata sandi, abaikan email ini dan akun Anda tetap aman.
        </p>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">
        <p style="font-size: 11px; color: #94a3b8; text-align: center;">
            Jika tombol di atas tidak dapat diklik, salin dan buka tautan berikut di peramban Anda:<br>
            <a href="{{ $resetUrl }}" style="color: #0284c7; word-break: break-all;">{{ $resetUrl }}</a>
        </p>
    </div>
</body>
</html>

