@extends('layouts.app')

@section('header-title', 'Cetak Laporan Puskesmas')

@section('content')
    <div class="card" style="margin-bottom: 32px;">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Pusat Pelaporan Terpadu</h3>
        <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.6;">
            Silakan pilih modul laporan di bawah ini untuk mencetak salinan rekapitulasi data resmi. Halaman cetak dirancang ramah cetak (print-friendly) untuk mempermudah pencetakan fisik atau penyimpanan digital dalam format PDF via dialog browser (Ctrl+P).
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 32px;">
        <!-- Card Module 1 -->
        <div class="card" style="display: flex; flex-direction: column; gap: 16px; justify-content: space-between;">
            <div>
                <div class="stat-icon teal" style="margin-bottom: 16px;"><i class="ri-capsule-line"></i></div>
                <h4 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Laporan Stok Obat</h4>
                <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.5;">
                    Merekap seluruh data stok obat aktif, status kritis/aman obat, harga beli/jual, serta tanggal kadaluarsa masing-masing obat.
                </p>
            </div>
            <a href="{{ route('kepala.reports.export', ['module' => 'obat', 'format' => 'print']) }}" target="_blank" class="btn btn-primary" style="justify-content: center; width: 100%;">
                <i class="ri-printer-line"></i> Cetak Laporan Obat
            </a>
        </div>

        <!-- Card Module 2 -->
        <div class="card" style="display: flex; flex-direction: column; gap: 16px; justify-content: space-between;">
            <div>
                <div class="stat-icon red" style="margin-bottom: 16px;"><i class="ri-virus-line"></i></div>
                <h4 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Laporan Sebaran Kasus Penyakit</h4>
                <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.5;">
                    Merekap data laporan kasus penyakit, identitas pasien, tingkat keparahan kasus, serta rekomendasi penanganan medis dari Dokter.
                </p>
            </div>
            <a href="{{ route('kepala.reports.export', ['module' => 'penyakit', 'format' => 'print']) }}" target="_blank" class="btn btn-primary" style="justify-content: center; width: 100%;">
                <i class="ri-printer-line"></i> Cetak Laporan Penyakit
            </a>
        </div>

        <!-- Card Module 3 -->
        <div class="card" style="display: flex; flex-direction: column; gap: 16px; justify-content: space-between;">
            <div>
                <div class="stat-icon orange" style="margin-bottom: 16px;"><i class="ri-notification-3-line"></i></div>
                <h4 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Laporan Imunisasi Anak</h4>
                <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.5;">
                    Merekap seluruh riwayat pelaksanaan vaksinasi imunisasi anak, jadwal terencana, tanggal realisasi suntikan, serta no batch vaksin.
                </p>
            </div>
            <a href="{{ route('kepala.reports.export', ['module' => 'imunisasi', 'format' => 'print']) }}" target="_blank" class="btn btn-primary" style="justify-content: center; width: 100%;">
                <i class="ri-printer-line"></i> Cetak Laporan Imunisasi
            </a>
        </div>
    </div>
@endsection
