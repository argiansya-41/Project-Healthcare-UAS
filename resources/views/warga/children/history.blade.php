@extends('layouts.app')

@section('header-title', 'Riwayat Imunisasi Anak')

@section('content')
    <div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
        
        <!-- Profile block -->
        <div class="card" style="display: flex; align-items: center; justify-content: space-between; border-left: 5px solid var(--accent-color);">
            <div>
                <a href="{{ route('warga.children.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px; margin-bottom: 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
                <h3 style="font-size: 20px; font-weight: 800;">{{ $child->name }}</h3>
                <p style="color: var(--text-secondary); font-size: 13px; margin-top: 4px;">
                    NIK: {{ $child->nik ?? '-' }} | Gender: {{ $child->gender == 'L' ? 'Laki-laki' : 'Perempuan' }} | Usia: {{ $child->getAgeMonths() }} Bulan
                </p>
            </div>
            <div style="text-align: right;">
                <span style="font-size: 12px; color: var(--text-secondary);">Berat Lahir</span>
                <p style="font-size: 20px; font-weight: 700; color: var(--text-primary);">{{ $child->birth_weight ?? '-' }} kg</p>
            </div>
        </div>

        <!-- History cards / timeline -->
        <div class="card">
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px;">Catatan Pelaksanaan Imunisasi & Jadwal</h3>

            <div style="display: flex; flex-direction: column; gap: 20px;">
                @forelse($records as $rec)
                    <div style="display: flex; border: 1px solid var(--card-border); border-radius: 16px; overflow: hidden; background-color: #ffffff;">
                        <!-- Status color bar -->
                        <div style="width: 8px; background-color: {{ $rec->status === 'completed' ? 'var(--success)' : ($rec->status === 'missed' ? 'var(--danger)' : 'var(--warning)') }}"></div>
                        
                        <div style="padding: 20px; flex-grow: 1; display: grid; grid-template-columns: 2fr 1fr; gap: 16px; align-items: center;">
                            <div>
                                <span class="badge {{ $rec->status === 'completed' ? 'badge-success' : ($rec->status === 'missed' ? 'badge-danger' : 'badge-warning') }}" style="margin-bottom: 8px;">
                                    {{ $rec->status }}
                                </span>
                                
                                <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">{{ $rec->vaccine->name }}</h4>
                                <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.5;">{{ $rec->vaccine->description }}</p>
                                
                                @if($rec->notes)
                                    <p style="font-size: 13px; margin-top: 12px; color: var(--text-primary); font-style: italic; background-color: #f8fafc; padding: 8px; border-radius: 6px;">
                                        "{{ $rec->notes }}"
                                    </p>
                                @endif
                            </div>

                            <div style="text-align: right; font-size: 13px;">
                                <p style="color: var(--text-secondary)">Rencana Jadwal:</p>
                                <p style="font-weight: 600; margin-bottom: 8px;">{{ $rec->scheduled_date->format('d/m/Y') }}</p>

                                @if($rec->status === 'completed')
                                    <p style="color: var(--text-secondary)">Pemberian Riil:</p>
                                    <p style="font-weight: 700; color: var(--success);">{{ $rec->administered_date->format('d/m/Y') }}</p>
                                    
                                    @if($rec->batch_number)
                                        <p style="color: var(--text-secondary); margin-top: 4px;">Batch: <code>{{ $rec->batch_number }}</code></p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 32px; color: var(--text-secondary);">
                        Belum ada catatan pelaksanaan maupun jadwal vaksinasi yang diinput petugas.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
