<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Warga - HealthCare</title>
    
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
            padding: 40px 24px;
        }

        .auth-card {
            background: var(--card-bg);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid var(--card-border);
            border-radius: 30px;
            padding: 40px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.03), inset 0 1px 0 rgba(255, 255, 255, 0.6);
            display: flex;
            flex-direction: column;
            gap: 28px;
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
            margin-bottom: 12px;
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
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-main);
        }

        .form-control {
            width: 100%;
            background-color: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 14px;
            padding: 12px 16px;
            font-size: 14px;
            color: var(--text-main);
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.15);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg fill='%230f172a' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/><path d='M0 0h24v24H0z' fill='none'/></svg>");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        .btn-submit {
            grid-column: span 2;
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: #ffffff;
            border: none;
            padding: 14px;
            border-radius: 50px; /* Pill button */
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

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.full-width, .btn-submit {
                grid-column: span 1;
            }
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
            <h2>Pendaftaran Warga</h2>
            <p>Lengkapi formulir untuk membuat akun layanan</p>
        </div>

        @if($errors->any())
            <div class="alert">
                <i class="ri-error-warning-fill"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Nama Lengkap Anda" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="nik">Nomor Induk Kependudukan (NIK)</label>
                    <input type="text" id="nik" name="nik" class="form-control" placeholder="16 Digit NIK" maxlength="16" value="{{ old('nik') }}" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">No. Handphone / WhatsApp</label>
                    <input type="text" id="phone_number" name="phone_number" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('phone_number') }}" required>
                </div>

                <div class="form-group">
                    <label for="gender">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="" disabled selected>Pilih jenis kelamin</option>
                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="address">Alamat Domisili</label>
                    <input type="text" id="address" name="address" class="form-control" placeholder="Nama Jalan, Kelurahan, Kecamatan" value="{{ old('address') }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Min. 8 karakter" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                </div>

                <button type="submit" class="btn-submit">Daftar Akun</button>
            </div>
        </form>

        <div class="auth-footer">
            Sudah memiliki akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </div>

</body>
</html>
