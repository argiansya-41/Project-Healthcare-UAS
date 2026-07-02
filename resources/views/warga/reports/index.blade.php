@extends('layouts.app')

@section('header-title', 'Riwayat Laporan Penyakit')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
                <h3 style="font-size: 18px; font-weight: 700;">Daftar Pelaporan Saya</h3>
            </div>
            <a href="{{ route('warga.reports.create') }}" class="btn btn-primary">
                <i class="ri-add-circle-line"></i> Buat Laporan Baru
            </a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal Sakit</th>
                        <th>Nama Pasien</th>
                        <th>NIK</th>
                        <th>Penyakit</th>
                        <th>Keparahan</th>
                        <th>Status Verifikasi</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $rep)
                        <tr>
                            <td>{{ $rep->report_date->format('d/m/Y') }}</td>
                            <td><strong>{{ $rep->patient_name }}</strong> <br><small style="color: var(--text-secondary)">{{ $rep->patient_age }} Tahun, {{ $rep->patient_gender }}</small></td>
                            <td>{{ $rep->patient_nik }}</td>
                            <td><span class="badge badge-info" style="background-color: rgba(59, 130, 246, 0.1); color: var(--info);">{{ $rep->diseaseType->name }}</span></td>
                            <td>
                                @if($rep->severity === 'ringan')
                                    <span class="badge badge-info">Ringan</span>
                                @elseif($rep->severity === 'sedang')
                                    <span class="badge badge-warning">Sedang</span>
                                @else
                                    <span class="badge badge-danger">Berat</span>
                                @endif
                            </td>
                            <td>
                                @if($rep->status === 'verified')
                                    <span class="badge badge-success"><i class="ri-checkbox-circle-fill"></i> Terverifikasi</span>
                                @elseif($rep->status === 'rejected')
                                    <span class="badge badge-danger"><i class="ri-close-circle-fill"></i> Ditolak</span>
                                @else
                                    <span class="badge badge-warning"><i class="ri-time-fill"></i> Pending</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('warga.reports.show', $rep->id) }}" class="btn btn-secondary btn-sm" style="padding: 6px 12px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="ri-eye-line"></i> Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 32px;">
                                Belum ada riwayat pelaporan penyakit yang dikirimkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $reports->links() }}
        </div>
    </div>
@endsection
