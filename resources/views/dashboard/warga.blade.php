@extends('layouts.app')

@section('header-title', 'Portal Layanan Warga')

@section('content')
    <!-- Welcome card -->
    <div class="card" style="background: linear-gradient(135deg, rgba(13, 148, 136, 0.1) 0%, rgba(255,255,255,0.85) 100%); margin-bottom: 32px;">
        <h3 style="font-size: 20px; font-weight: 700; color: var(--accent-color); margin-bottom: 8px;">Halo, {{ auth()->user()->name }}!</h3>
        <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.6;">
            Selamat datang di Portal Kesehatan Warga. Di sini Anda dapat melihat data pendaftaran imunisasi anak Anda, memantau riwayat imunisasi yang telah diselesaikan, serta melihat pengingat jadwal imunisasi berikutnya agar tidak terlewat.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 1.2fr 2fr; gap: 32px;">
        <!-- Left Column: Children list -->
        <div class="card" style="height: fit-content;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700;">Data Anak Saya</h3>
                <span class="badge badge-info">{{ $stats['total_children'] }} Anak</span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 16px;">
                @forelse($stats['my_children'] as $child)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; border: 1px solid var(--card-border); border-radius: 16px; background-color: #ffffff;">
                        <div>
                            <strong style="font-size: 15px;">{{ $child->name }}</strong>
                            <br><small style="color: var(--text-secondary)">{{ $child->getAgeMonths() }} bulan ({{ $child->gender == 'L' ? 'Laki-laki' : 'Perempuan' }})</small>
                        </div>
                        <a href="{{ route('warga.history', ['child_id' => $child->id]) }}" class="btn btn-secondary btn-sm">
                            <i class="ri-history-line"></i> Riwayat
                        </a>
                    </div>
                @empty
                    <div style="text-align: center; padding: 24px; color: var(--text-secondary); border: 1px dashed var(--card-border); border-radius: 16px;">
                        Belum ada data anak terdaftar. Hubungi Petugas Imunisasi untuk mendaftarkan anak Anda.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Reminders / Schedules -->
        <div class="card">
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px;">Jadwal Imunisasi Mendatang</h3>
            
            <div class="table-responsive" style="margin-top: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Anak</th>
                            <th>Vaksin</th>
                            <th>Tanggal Jadwal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['upcoming_vaccinations'] as $vac)
                            <tr>
                                <td><strong>{{ $vac->child->name }}</strong></td>
                                <td><span class="badge badge-info">{{ $vac->vaccine->name }}</span></td>
                                <td>{{ $vac->scheduled_date->format('d F Y') }}</td>
                                <td><span class="badge badge-warning">Dijadwalkan</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 24px;">Belum ada jadwal imunisasi terdekat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection



