<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #000000;
            padding: 40px;
            margin: 0;
        }

        .header-kop {
            text-align: center;
            border-bottom: 3px double #000000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header-kop h1 {
            font-size: 22px;
            margin: 0 0 5px;
            text-transform: uppercase;
        }

        .header-kop p {
            font-size: 13px;
            margin: 0;
        }

        .report-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .report-title h2 {
            font-size: 18px;
            margin: 0 0 10px;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .report-title span {
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        table th, table td {
            border: 1px solid #000000;
            padding: 8px 12px;
            font-size: 13px;
        }

        table th {
            background-color: #f2f2f2;
            text-align: left;
            font-weight: bold;
        }

        .footer-sign {
            margin-top: 50px;
            float: right;
            text-align: center;
            width: 250px;
            font-size: 14px;
        }

        .footer-sign .space {
            height: 80px;
        }

        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #0d9488; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">Cetak Laporan</button>
    </div>

    <!-- Kop Surat -->
    <div class="header-kop">
        <h1>Pemerintah Kota / Kabupaten Jakarta</h1>
        <h1>Puskesmas HealthCare Sehat Sejahtera</h1>
        <p>Jl. Kesehatan No. 1, Jakarta Selatan - Telp: (021) 555-1234 - Email: info@puskesmas-sehat.go.id</p>
    </div>

    <!-- Judul Laporan -->
    <div class="report-title">
        <h2>{{ $title }}</h2>
        <span>Dicetak pada tanggal: {{ now()->translatedFormat('d F Y H:i') }} WIB</span>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Obat</th>
                <th>Nama Obat</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th>Kadaluarsa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $med)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><code>{{ $med->code }}</code></td>
                    <td>{{ $med->name }}</td>
                    <td>{{ $med->category->name }}</td>
                    <td>{{ $med->unit->name }}</td>
                    <td>Rp{{ number_format($med->purchase_price, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($med->selling_price, 0, ',', '.') }}</td>
                    <td><strong>{{ $med->stock }}</strong></td>
                    <td>{{ $med->expiration_date->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="footer-sign">
        <p>Jakarta, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Kepala Puskesmas,</p>
        <div class="space"></div>
        <p><strong><u>Dr. H. Ahmad Fauzi</u></strong></p>
        <p>NIP. 19750812 200003 1 002</p>
    </div>

    <script>
        // Auto trigger print when loaded
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
