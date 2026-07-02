<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - HealthCare</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons (Remix Icon) -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root {
            --primary: #0f766e; /* Deep Teal */
            --primary-light: #0891b2; /* Secondary blue-teal */
            --primary-hover: #0d9488;
            --bg-gradient: 
                radial-gradient(circle at 10% 20%, rgba(204, 251, 241, 0.6) 0%, transparent 40%),
                radial-gradient(circle at 90% 10%, rgba(224, 242, 254, 0.6) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(243, 232, 255, 0.5) 0%, transparent 50%),
                #f1f5f9;
            --text-main: #0f172a;
            --text-muted: #475569;
            --card-bg: rgba(255, 255, 255, 0.6);
            --card-border: rgba(255, 255, 255, 0.5);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-card {
            background: var(--card-bg);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid var(--card-border);
            border-radius: 30px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.03), inset 0 1px 0 rgba(255, 255, 255, 0.6);
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .auth-header {
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .logo i {
            color: var(--primary);
            font-size: 24px;
        }

        .auth-header h2 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
        }

        .auth-header p {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
        }

        .input-group {
            position: relative;
        }

        .input-group > i:first-child {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 18px;
        }

        .form-control {
            width: 100%;
            background-color: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 14px;
            padding: 12px 16px 12px 48px;
            font-size: 14px;
            color: var(--text-main);
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.15);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: #ffffff;
            border: none;
            padding: 14px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(15, 118, 110, 0.25);
            transition: all 0.25s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--primary-hover) 0%, #06b6d4 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(15, 118, 110, 0.35);
        }

        .auth-footer {
            text-align: center;
            font-size: 14px;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }

        .auth-footer a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            background-color: rgba(239, 68, 68, 0.08);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.15);
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <div class="auth-header">
            <div class="logo">
                <i class="ri-heart-pulse-fill"></i>
                <span>HealthCare</span>
            </div>
            <h2>Lupa Password</h2>
            <p>Masukkan Email & NIK terdaftar Anda untuk verifikasi identitas dan mengatur ulang password.</p>
        </div>

        @if($errors->any())
            <div class="alert">
                <i class="ri-error-warning-fill"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <div class="input-group">
                    <i class="ri-mail-line"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="nik">NIK (16 Digit Nomor Induk Kependudukan)</label>
                <div class="input-group">
                    <i class="ri-key-line"></i>
                    <input type="text" id="nik" name="nik" class="form-control" placeholder="320101XXXXXXXXXX" maxlength="16" value="{{ old('nik') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password Baru</label>
                <div class="input-group" style="position: relative;">
                    <i class="ri-lock-2-line"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" style="padding-right: 48px;" required>
                    <button type="button" class="togglePassword" data-target="password" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; padding: 0;">
                        <i class="ri-eye-off-line"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <div class="input-group" style="position: relative;">
                    <i class="ri-lock-check-line"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="••••••••" style="padding-right: 48px;" required>
                    <button type="button" class="togglePassword" data-target="password_confirmation" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; padding: 0;">
                        <i class="ri-eye-off-line"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">Set Ulang Password</button>
        </form>

        <div class="auth-footer">
            Kembali ke halaman <a href="{{ route('login') }}">Masuk</a>
        </div>
    </div>

    <script>
        document.querySelectorAll('.togglePassword').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (input && icon) {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    
                    if (type === 'password') {
                        icon.className = 'ri-eye-off-line';
                    } else {
                        icon.className = 'ri-eye-line';
                    }
                }
            });
        });
    </script>
</body>
</html>
