@extends('layouts.app')

@section('header-title', 'Dashboard Petugas Medis')

@section('content')
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="card stat-card">
            <div class="stat-info">
                <h4>Total Kasus Penyakit</h4>
                <p>{{ $stats['total_reports'] }}</p>
            </div>
            <div class="stat-icon blue">
                <i class="ri-virus-fill"></i>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <h4>Menunggu Verifikasi</h4>
                <p style="color: {{ $stats['pending_reports'] > 0 ? 'var(--warning)' : 'inherit' }}">{{ $stats['pending_reports'] }}</p>
            </div>
            <div class="stat-icon orange">
                <i class="ri-time-fill"></i>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <h4>Butuh Rekomendasi Medis</h4>
                <p style="color: {{ $stats['reports_need_treatment'] > 0 ? 'var(--danger)' : 'inherit' }}">{{ $stats['reports_need_treatment'] }}</p>
            </div>
            <div class="stat-icon red">
                <i class="ri-heart-pulse-fill"></i>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <h4>Anak Terdaftar</h4>
                <p>{{ $stats['total_children'] }}</p>
            </div>
            <div class="stat-icon teal">
                <i class="ri-parent-fill"></i>
            </div>
        </div>
    </div>

    <!-- Alerts Container -->
    <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 32px;">
        @if($stats['pending_reports'] > 0)
            <div class="alert alert-warning" style="margin-bottom: 0;">
                <i class="ri-notification-3-fill" style="font-size: 20px;"></i>
                <div>
                    Terdapat <strong>{{ $stats['pending_reports'] }}</strong> laporan kasus penyakit masuk yang memerlukan verifikasi lapangan/data.
                    <a href="{{ route('kesehatan.verification.index') }}" style="color: inherit; font-weight: 700; text-decoration: underline; margin-left: 8px;">Mulai Verifikasi &rarr;</a>
                </div>
            </div>
        @endif


    </div>

    <!-- Main Module Section 1: Pelaporan Penyakit -->
    <h3 style="font-size: 20px; font-weight: 800; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; color: var(--text-primary)">
        <i class="ri-virus-line" style="color: var(--accent-color)"></i> Pelaporan & Verifikasi Kasus Penyakit
    </h3>
    <div style="display: grid; grid-template-columns: 2fr 1.2fr; gap: 32px; margin-bottom: 48px;">
        <!-- Recent Disease Reports -->
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 700;">Laporan Kasus Penyakit Terkini</h3>
                <a href="{{ route('kesehatan.reports.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
            </div>

            <div class="table-responsive" style="margin-top: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pasien</th>
                            <th>Penyakit</th>
                            <th>Tingkat Keparahan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['recent_reports'] as $report)
                            <tr>
                                <td>{{ $report->report_date->format('d/m/Y') }}</td>
                                <td>
                                    <strong>{{ $report->patient_name }}</strong>
                                    <br><small style="color: var(--text-secondary)">{{ $report->patient_age }} Tahun, {{ $report->patient_gender }}</small>
                                </td>
                                <td>{{ $report->diseaseType->name }}</td>
                                <td>
                                    @if($report->severity === 'ringan')
                                        <span class="badge badge-info">Ringan</span>
                                    @elseif($report->severity === 'sedang')
                                        <span class="badge badge-warning">Sedang</span>
                                    @else
                                        <span class="badge badge-danger">Berat</span>
                                    @endif
                                </td>
                                <td>
                                    @if($report->status === 'verified')
                                        <span class="badge badge-success">Terverifikasi</span>
                                    @elseif($report->status === 'rejected')
                                        <span class="badge badge-danger">Ditolak</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary);">Belum ada laporan kasus penyakit.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Severity Chart Card -->
        <div class="card" style="display: flex; flex-direction: column; gap: 20px; align-items: center;">
            <div style="width: 100%; display: flex; flex-direction: column; gap: 8px;">
                <h3 style="font-size: 16px; font-weight: 700;">Statistik Tingkat Keparahan</h3>
                <form action="{{ route('dashboard') }}" method="GET" id="monthFilterForm" style="width: 100%;">
                    <select name="severity_month" class="form-control" onchange="this.form.submit()" style="font-size: 13px; padding: 8px 12px; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                        <option value="">Semua Bulan</option>
                        @foreach([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ] as $num => $name)
                            <option value="{{ $num }}" {{ request('severity_month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            
            <div style="width: 100%; max-width: 180px; height: 180px; position: relative;">
                <canvas id="severityChart"></canvas>
            </div>
            
            <div style="display: flex; flex-direction: column; width: 100%; gap: 12px; margin-top: 8px;">
                <a href="{{ route('kesehatan.reports.create') }}" class="btn btn-primary" style="justify-content: center; width: 100%;">
                    <i class="ri-add-circle-line"></i> Input Laporan Baru
                </a>
                <a href="{{ route('kesehatan.map') }}" class="btn btn-secondary" style="justify-content: center; width: 100%;">
                    <i class="ri-map-pin-2-line"></i> Lihat Peta Sebaran Kasus
                </a>
            </div>
        </div>
    </div>


    <!-- Main Module Section 3: Layanan Imunisasi -->
    <h3 style="font-size: 20px; font-weight: 800; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; color: var(--text-primary)">
        <i class="ri-calendar-todo-line" style="color: var(--info)"></i> Jadwal & Reminder Imunisasi Anak
    </h3>
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
        <!-- Upcoming Schedules Table -->
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 700;">Jadwal Imunisasi Terdekat</h3>
                <a href="{{ route('imunisasi.schedules.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
            </div>

            <div class="table-responsive" style="margin-top: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal Jadwal</th>
                            <th>Nama Anak</th>
                            <th>Vaksin</th>
                            <th>Orang Tua</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['upcoming_schedules'] as $sch)
                            <tr>
                                <td><strong>{{ $sch->scheduled_date->format('d/m/Y') }}</strong></td>
                                <td>{{ $sch->child->name }}</td>
                                <td><span class="badge badge-info">{{ $sch->vaccine->name }}</span></td>
                                <td>{{ $sch->child->parent->name }} <br><small style="color: var(--text-secondary)">{{ $sch->child->parent->phone_number }}</small></td>
                                <td><span class="badge badge-warning">Dijadwalkan</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary);">Belum ada jadwal imunisasi terdekat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Shortcuts & Chart -->
        <div class="card" style="height: fit-content; display: flex; flex-direction: column; gap: 20px; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 700; align-self: flex-start;">Rasio Status Imunisasi</h3>
            
            <div style="width: 100%; height: 160px; position: relative;">
                <canvas id="ratioChart"></canvas>
            </div>

            <div style="display: flex; flex-direction: column; width: 100%; gap: 12px; margin-top: 8px;">
                <a href="{{ route('imunisasi.children.create') }}" class="btn btn-primary" style="justify-content: center; width: 100%;">
                    <i class="ri-user-add-line"></i> Daftarkan Anak Baru
                </a>
                <a href="{{ route('imunisasi.schedules.create') }}" class="btn btn-secondary" style="justify-content: center; width: 100%;">
                    <i class="ri-calendar-add-line"></i> Buat Jadwal Vaksin
                </a>
                <a href="{{ route('imunisasi.reminders.index') }}" class="btn btn-secondary" style="justify-content: center; width: 100%; background: transparent; border: 1px solid var(--card-border);">
                    <i class="ri-notification-badge-line"></i> Kirim Reminder Manual
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Doughnut Chart - Severity
        const ctxSeverity = document.getElementById('severityChart').getContext('2d');
        const severityChart = new Chart(ctxSeverity, {
            type: 'doughnut',
            data: {
                labels: ['Ringan', 'Sedang', 'Berat'],
                datasets: [{
                    data: [
                        {{ $stats['severity_stats']['ringan'] }},
                        {{ $stats['severity_stats']['sedang'] }},
                        {{ $stats['severity_stats']['berat'] }}
                    ],
                    backgroundColor: ['#3b82f6', '#f59e0b', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: {
                                family: 'Plus Jakarta Sans',
                                size: 11
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });

        // Bar Chart - Immunization Ratio
        const ctxRatio = document.getElementById('ratioChart').getContext('2d');
        const ratioChart = new Chart(ctxRatio, {
            type: 'bar',
            data: {
                labels: ['Jadwal', 'Selesai', 'Terlewat'],
                datasets: [{
                    label: 'Jumlah Vaksinasi',
                    data: [
                        {{ $stats['scheduled_count'] }},
                        {{ $stats['completed_count'] }},
                        {{ $stats['missed_count'] }}
                    ],
                    backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
