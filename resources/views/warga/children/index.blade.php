@extends('layouts.app')

@section('header-title', 'Data Anak Saya')

@section('content')
    <div class="card">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px;">Daftar Anak Terdaftar</h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
            @forelse($children as $child)
                <div class="card" style="border: 1px solid var(--card-border); background-color: #ffffff; display: flex; flex-direction: column; gap: 16px; transition: all 0.2s ease;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h4 style="font-size: 18px; font-weight: 700; color: var(--accent-color);">{{ $child->name }}</h4>
                            <small style="color: var(--text-secondary)">NIK: {{ $child->nik ?? '-' }}</small>
                        </div>
                        <span class="badge badge-info">{{ $child->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    
                    <div style="border-top: 1px solid var(--card-border); padding-top: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <span style="font-size: 11px; color: var(--text-secondary); text-transform: uppercase;">Usia:</span>
                            <p style="font-size: 14px; font-weight: 600;">{{ $child->getAgeMonths() }} Bulan</p>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-secondary); text-transform: uppercase;">Tgl Lahir:</span>
                            <p style="font-size: 14px; font-weight: 600;">{{ $child->date_of_birth->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-secondary); text-transform: uppercase;">Lahir Di:</span>
                            <p style="font-size: 14px; font-weight: 600;">{{ $child->place_of_birth ?? '-' }}</p>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-secondary); text-transform: uppercase;">Berat Lahir:</span>
                            <p style="font-size: 14px; font-weight: 600;">{{ $child->birth_weight ? $child->birth_weight . ' kg' : '-' }}</p>
                        </div>
                    </div>

                    <a href="{{ route('warga.history', ['child_id' => $child->id]) }}" class="btn btn-primary" style="justify-content: center; margin-top: 8px;">
                        <i class="ri-history-line"></i> Lihat Riwayat & Jadwal Vaksin
                    </a>
                </div>
            @empty
                <div style="grid-column: span 3; text-align: center; padding: 48px; color: var(--text-secondary); border: 1px dashed var(--card-border); border-radius: 20px;">
                    <i class="ri-parent-line" style="font-size: 48px; color: var(--text-secondary);"></i>
                    <p style="margin-top: 12px; font-weight: 500;">Belum ada data anak terdaftar.</p>
                    <p style="font-size: 13px; color: var(--text-secondary)">Hubungi petugas di Puskesmas terdekat untuk mendaftarkan data bayi/anak Anda ke sistem.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
