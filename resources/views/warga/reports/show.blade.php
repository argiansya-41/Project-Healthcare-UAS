@extends('layouts.app')

@section('header-title', 'Detail Laporan Penyakit')

@section('content')
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px; max-width: 1100px; margin: 0 auto;">
        
        <!-- Left: Case Details Card -->
        <div class="card" style="display: flex; flex-direction: column; gap: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--card-border); padding-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <a href="{{ route('warga.reports.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
                    <h3 style="font-size: 18px; font-weight: 700;">Detail Laporan Penyakit #{{ $report->id }}</h3>
                </div>
                
                @if($report->status === 'verified')
                    <span class="badge badge-success">Terverifikasi</span>
                @elseif($report->status === 'rejected')
                    <span class="badge badge-danger">Ditolak</span>
                @else
                    <span class="badge badge-warning">Pending</span>
                @endif
            </div>

            <!-- Patient Profile -->
            <div>
                <h4 style="font-size: 14px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 12px;">Profil Pasien</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <span style="font-size: 13px; color: var(--text-secondary)">Nama Pasien:</span>
                        <p style="font-weight: 600; font-size: 15px;">{{ $report->patient_name }}</p>
                    </div>
                    <div>
                        <span style="font-size: 13px; color: var(--text-secondary)">NIK Pasien:</span>
                        <p style="font-weight: 600; font-size: 15px;">{{ $report->patient_nik }}</p>
                    </div>
                    <div>
                        <span style="font-size: 13px; color: var(--text-secondary)">Umur & Jenis Kelamin:</span>
                        <p style="font-weight: 600; font-size: 15px;">{{ $report->patient_age }} Tahun, {{ $report->patient_gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <span style="font-size: 13px; color: var(--text-secondary)">Alamat Tinggal:</span>
                        <p style="font-weight: 600; font-size: 15px;">{{ $report->patient_address }}</p>
                    </div>
                    <div>
                        <span style="font-size: 13px; color: var(--text-secondary)">Wilayah Admin (Desa, Kec, Kab):</span>
                        <p style="font-weight: 600; font-size: 15px;">
                            @if($report->village)
                                {{ $report->village->name }}, Kec. {{ $report->village->kecamatan }}, {{ $report->village->kabupaten }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Medical Profile -->
            <div style="border-top: 1px solid var(--card-border); padding-top: 20px;">
                <h4 style="font-size: 14px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 12px;">Informasi Medis</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <span style="font-size: 13px; color: var(--text-secondary)">Jenis Penyakit (Diagnosa):</span>
                        <p style="font-weight: 700; font-size: 15px; color: var(--danger);">{{ $report->diseaseType->name }} ({{ $report->diseaseType->code }})</p>
                    </div>
                    <div>
                        <span style="font-size: 13px; color: var(--text-secondary)">Tingkat Keparahan Kasus:</span>
                        <p style="font-weight: 600; font-size: 15px;">
                            @if($report->severity === 'ringan')
                                <span style="color: var(--info)">Ringan</span>
                            @elseif($report->severity === 'sedang')
                                <span style="color: var(--warning)">Sedang</span>
                            @else
                                <span style="color: var(--danger); font-weight: 700;">Berat</span>
                            @endif
                        </p>
                    </div>
                    <div style="grid-column: span 2;">
                        <span style="font-size: 13px; color: var(--text-secondary)">Detail Gejala Klinis:</span>
                        <p style="font-size: 14px; line-height: 1.6; background-color: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid var(--card-border); margin-top: 4px;">
                            {{ $report->symptoms }}
                        </p>
                    </div>
                </div>
            </div>


        </div>

        <!-- Right: Verification Details Card -->
        <div class="card" style="height: fit-content; display: flex; flex-direction: column; gap: 20px;">
            <h3 style="font-size: 16px; font-weight: 700; border-bottom: 1px solid var(--card-border); padding-bottom: 12px;">Administrasi Kasus</h3>
            
            <div>
                <span style="font-size: 12px; color: var(--text-secondary)">Dilaporkan Pada:</span>
                <p style="font-size: 14px; font-weight: 600;">{{ $report->created_at->format('d/m/Y H:i') }} WIB</p>
            </div>

            @if($report->verified_by)
                <div style="border-top: 1px solid var(--card-border); padding-top: 16px;">
                    <span style="font-size: 12px; color: var(--text-secondary)">Diverifikasi Oleh:</span>
                    <p style="font-size: 14px; font-weight: 600;">{{ $report->verifiedBy->name }}</p>
                    <small style="color: var(--text-secondary)">Pada {{ $report->updated_at->format('d/m/Y H:i') }} WIB</small>
                    
                    <div style="margin-top: 12px;">
                        <span style="font-size: 12px; color: var(--text-secondary)">Catatan Verifikasi:</span>
                        <p style="font-size: 13px; font-style: italic; color: var(--text-primary); background-color: #f8fafc; padding: 8px; border-radius: 6px; margin-top: 4px;">
                            "{{ $report->verification_notes }}"
                        </p>
                    </div>
                </div>
            @endif

            <!-- GIS Coordinates info -->
            @if($report->latitude && $report->longitude)
                <div style="border-top: 1px solid var(--card-border); padding-top: 16px;">
                    <span style="font-size: 12px; color: var(--text-secondary)"><i class="ri-map-pin-line"></i> Koordinat Wilayah Laporan:</span>
                    <p style="font-size: 13px; font-family: monospace; margin-top: 4px; margin-bottom: 0;">Lat: {{ $report->latitude }}, Lng: {{ $report->longitude }}</p>
                    @if($report->village)
                        <small style="color: var(--text-secondary); display: block; margin-top: 4px;">Pusat: Desa {{ $report->village->name }} (Kec. {{ $report->village->kecamatan }}, {{ $report->village->kabupaten }})</small>
                    @endif
                </div>
            @endif
        </div>

    </div>
@endsection
