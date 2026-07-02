@extends('layouts.app')

@section('header-title', 'Dashboard Administrator')

@section('content')
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="card stat-card">
            <div class="stat-info">
                <h4>Total Pengguna</h4>
                <p>{{ $stats['total_users'] }}</p>
            </div>
            <div class="stat-icon blue">
                <i class="ri-user-heart-fill"></i>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <h4>Total Obat</h4>
                <p>{{ $stats['total_medicines'] }}</p>
            </div>
            <div class="stat-icon teal">
                <i class="ri-capsule-fill"></i>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <h4>Kasus Penyakit</h4>
                <p>{{ $stats['total_disease_reports'] }}</p>
            </div>
            <div class="stat-icon red">
                <i class="ri-virus-fill"></i>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <h4>Anak Terdaftar</h4>
                <p>{{ $stats['total_children'] }}</p>
            </div>
            <div class="stat-icon orange">
                <i class="ri-parent-fill"></i>
            </div>
        </div>
    </div>

    <!-- Alert for Restock Requests -->
    @if($stats['pending_restocks'] > 0)
        <div class="alert alert-danger" style="margin-bottom: 32px;">
            <i class="ri-notification-badge-fill" style="font-size: 20px;"></i>
            <div>
                Ada <strong>{{ $stats['pending_restocks'] }}</strong> pengajuan restock obat pending yang memerlukan persetujuan Anda. 
                <a href="{{ route('admin.restock.index') }}" style="color: inherit; font-weight: 700; text-decoration: underline; margin-left: 8px;">Proses Sekarang &rarr;</a>
            </div>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
        <!-- Recent Audit Trails -->
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700;">Riwayat Aktivitas Sistem Terbaru</h3>
                <a href="{{ route('admin.logs') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
            </div>

            <div class="table-responsive" style="margin-top: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['recent_logs'] as $log)
                            <tr>
                                <td style="white-space: nowrap;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong>{{ $log->user ? $log->user->name : 'Sistem' }}</strong>
                                    <br><small style="color: var(--text-secondary)">{{ $log->user ? $log->user->role : '' }}</small>
                                </td>
                                <td><span class="badge badge-info">{{ $log->action }}</span></td>
                                <td>{{ $log->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-secondary);">Belum ada riwayat aktivitas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Shortcuts -->
        <div class="card" style="height: fit-content;">
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px;">Navigasi Cepat</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary" style="justify-content: center; width: 100%;">
                    <i class="ri-user-add-line"></i> Kelola User
                </a>
                <a href="{{ route('admin.restock.index') }}" class="btn btn-secondary" style="justify-content: center; width: 100%;">
                    <i class="ri-survey-line"></i> Verifikasi Restock Obat
                </a>
                <a href="{{ route('admin.logs') }}" class="btn btn-secondary" style="justify-content: center; width: 100%;">
                    <i class="ri-history-line"></i> Audit Trail Sistem
                </a>
            </div>
        </div>
    </div>
@endsection
