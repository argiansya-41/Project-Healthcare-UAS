@extends('layouts.app')

@section('header-title', 'Dashboard Petugas Apotek')

@section('content')
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="card stat-card">
            <div class="stat-info">
                <h4>Total Varian Obat</h4>
                <p>{{ $stats['total_medicines'] }}</p>
            </div>
            <div class="stat-icon teal">
                <i class="ri-capsule-fill"></i>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <h4>Kategori Obat</h4>
                <p>{{ $stats['total_categories'] }}</p>
            </div>
            <div class="stat-icon blue">
                <i class="ri-folders-fill"></i>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <h4>Stok Hampir Habis</h4>
                <p style="color: {{ $stats['low_stock_count'] > 0 ? 'var(--danger)' : 'inherit' }}">{{ $stats['low_stock_count'] }}</p>
            </div>
            <div class="stat-icon orange">
                <i class="ri-error-warning-fill"></i>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <h4>Obat Kadaluarsa</h4>
                <p style="color: {{ $stats['expired_count'] > 0 ? 'var(--danger)' : 'inherit' }}">{{ $stats['expired_count'] }}</p>
            </div>
            <div class="stat-icon red">
                <i class="ri-calendar-close-fill"></i>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    @if($stats['low_stock_count'] > 0)
        <div class="alert alert-danger" style="margin-bottom: 24px;">
            <i class="ri-alert-fill" style="font-size: 20px;"></i>
            <div>
                Ada <strong>{{ $stats['low_stock_count'] }}</strong> jenis obat dengan stok di bawah batas minimum. 
                Silakan lakukan pengajuan restock ke Admin.
                <a href="{{ route('apotek.restock-requests.create') }}" style="color: inherit; font-weight: 700; text-decoration: underline; margin-left: 8px;">Ajukan Restock &rarr;</a>
            </div>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
        <!-- Recent Medicines Table -->
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700;">Data Obat Terkini</h3>
                <a href="{{ route('apotek.medicines.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
            </div>

            <div class="table-responsive" style="margin-top: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['recent_medicines'] as $med)
                            <tr>
                                <td><code>{{ $med->code }}</code></td>
                                <td><strong>{{ $med->name }}</strong></td>
                                <td>{{ $med->category->name }}</td>
                                <td>{{ $med->stock }} {{ $med->unit->abbreviation }}</td>
                                <td>
                                    @if($med->isExpired())
                                        <span class="badge badge-danger">Kadaluarsa</span>
                                    @elseif($med->isAlmostOutOfStock())
                                        <span class="badge badge-warning">Stok Rendah</span>
                                    @else
                                        <span class="badge badge-success">Aman</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary);">Belum ada data obat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Shortcuts -->
        <div class="card" style="height: fit-content;">
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px;">Aksi Cepat</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <a href="{{ route('apotek.transactions.create') }}" class="btn btn-primary" style="justify-content: center; width: 100%;">
                    <i class="ri-add-circle-line"></i> Catat Transaksi Baru
                </a>
                <a href="{{ route('apotek.medicines.create') }}" class="btn btn-secondary" style="justify-content: center; width: 100%;">
                    <i class="ri-add-line"></i> Tambah Obat Baru
                </a>
                <a href="{{ route('apotek.restock-requests.index') }}" class="btn btn-secondary" style="justify-content: center; width: 100%;">
                    <i class="ri-file-list-3-line"></i> Log Restock Obat ({{ $stats['pending_restock_requests'] }} pending)
                </a>
            </div>
        </div>
    </div>
@endsection
