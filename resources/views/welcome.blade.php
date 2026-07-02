<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthCare</title>
    
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
            --card-bg: #ffffff;
            --card-border: rgba(226, 232, 240, 0.8);
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
            flex-direction: column;
            justify-content: space-between;
            overflow-x: hidden;
        }

        header {
            padding: 32px 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 800;
            color: var(--text-main);
        }

        .logo i {
            color: var(--primary);
            font-size: 28px;
        }

        .nav-links {
            display: flex;
            gap: 24px;
            align-items: center;
        }

        .btn {
            padding: 12px 28px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-outline {
            color: var(--text-main);
            border: 1px solid rgba(15, 118, 110, 0.2);
            background-color: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .btn-outline:hover {
            background-color: rgba(15, 118, 110, 0.05);
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-solid {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(15, 118, 110, 0.25);
        }

        .btn-solid:hover {
            background: linear-gradient(135deg, var(--primary-hover) 0%, #06b6d4 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(15, 118, 110, 0.35);
        }

        main {
            padding: 0 64px 64px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 48px;
        }

        .hero-section {
            max-width: 900px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .badge {
            background-color: rgba(15, 118, 110, 0.08);
            color: var(--primary);
            border: 1px solid rgba(15, 118, 110, 0.15);
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            align-self: center;
            letter-spacing: 1px;
        }

        .hero-section h2 {
            font-size: 54px;
            font-weight: 800;
            line-height: 1.15;
            background: linear-gradient(135deg, var(--text-main) 40%, var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-section p {
            color: var(--text-muted);
            font-size: 18px;
            line-height: 1.6;
            max-width: 700px;
            margin: 0 auto;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 32px;
            width: 100%;
            max-width: 1100px;
        }

        .module-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 28px;
            padding: 36px;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02), inset 0 1px 0 rgba(255, 255, 255, 0.6);
            transition: all 0.3s ease;
        }

        .module-card:hover {
            transform: translateY(-8px);
            border-color: rgba(15, 118, 110, 0.25);
            box-shadow: 0 20px 40px rgba(15, 118, 110, 0.08);
        }

        .module-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            background-color: rgba(15, 118, 110, 0.08);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .module-card h3 {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-main);
        }

        .module-card p {
            color: var(--text-muted);
            font-size: 14.5px;
            line-height: 1.6;
        }

        footer {
            padding: 32px;
            text-align: center;
            font-size: 14px;
            color: var(--text-muted);
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 768px) {
            header {
                padding: 24px 16px;
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }
            .nav-links {
                width: 100%;
                justify-content: center;
                gap: 12px;
            }
            .btn {
                padding: 10px 20px;
                font-size: 13px;
                flex: 1;
                max-width: 180px;
                text-align: center;
            }
            main { padding: 0 16px 48px; }
            .hero-section h2 { font-size: 32px; }
            .hero-section p { font-size: 15px; }
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <i class="ri-heart-pulse-fill"></i>
            <span>HealthCare</span>
        </div>
        <div class="nav-links">
            <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn-solid">Registrasi Warga</a>
        </div>
    </header>

    <main>
        <div class="hero-section">
            <div class="badge">Layanan Kesehatan Digital</div>
            <h2>Solusi Terintegrasi untuk Puskesmas & Warga</h2>
            <p>Membantu pengelolaan ketersediaan obat, pelaporan cepat persebaran wabah penyakit, serta pengingat jadwal imunisasi anak secara otomatis.</p>
        </div>

        <div class="modules-grid">
            <div class="module-card">
                <div class="module-icon"><i class="ri-capsule-line"></i></div>
                <h3>Monitoring Obat</h3>
                <p>Mengelola ketersediaan stok obat secara real-time, mendeteksi obat kadaluarsa, serta mengirimkan notifikasi restock otomatis.</p>
            </div>
            
            <div class="module-card">
                <div class="module-icon"><i class="ri-virus-line"></i></div>
                <h3>Laporan Kasus Penyakit</h3>
                <p>Pemantauan dini persebaran penyakit berbasis wilayah secara real-time untuk mempercepat verifikasi kasus medis.</p>
            </div>

            <div class="module-card">
                <div class="module-icon"><i class="ri-notification-3-line"></i></div>
                <h3>Reminder Imunisasi</h3>
                <p>Jadwal imunisasi berkala yang teratur dengan sistem reminder dashboard dan email untuk mencegah kelalaian imunisasi anak.</p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 HealthCare. All Rights Reserved.</p>
    </footer>

</body>
</html>
