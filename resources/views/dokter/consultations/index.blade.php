@extends('layouts.app')

@section('header-title', 'Tanggapan Keluhan Imunisasi')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700;">Daftar Keluhan Pasca Imunisasi (KIPI)</h3>
            
            <!-- Filter buttons -->
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('dokter.consultations.index') }}" class="btn {{ !request('filter') ? 'btn-primary' : 'btn-secondary' }} btn-sm">Semua</a>
                <a href="{{ route('dokter.consultations.index', ['filter' => 'pending_treatment']) }}" class="btn {{ request('filter') == 'pending_treatment' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Belum Ditanggapi</a>
                <a href="{{ route('dokter.consultations.index', ['filter' => 'treated']) }}" class="btn {{ request('filter') == 'treated' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Sudah Ditanggapi</a>
            </div>
        </div>

        <!-- Vaccine Complaints Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Anak (Orang Tua)</th>
                        <th>Vaksin Imunisasi</th>
                        <th>Tanggal Imunisasi</th>
                        <th>Keluhan KIPI</th>
                        <th style="width: 35%;">Tanggapan Medis Dokter</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $comp)
                        <tr>
                            <td>
                                <strong>{{ $comp->child->name }}</strong>
                                <br><small style="color: var(--text-secondary)">Ortu: {{ $comp->child->parent->name }}</small>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $comp->vaccine->name }}</span>
                                <br><small style="color: var(--text-secondary)">Batch: {{ $comp->batch_number ?? '-' }}</small>
                            </td>
                            <td>
                                {{ $comp->administered_date ? $comp->administered_date->format('d/m/Y') : '' }}
                            </td>
                            <td>
                                <p style="max-width: 250px; display: inline-block; font-size: 13px;" title="{{ $comp->vaccine_complaint }}">
                                    {{ $comp->vaccine_complaint }}
                                </p>
                            </td>
                            <td>
                                @if($comp->doctor_response)
                                    <div style="background-color: rgba(16, 185, 129, 0.05); padding: 12px; border-radius: 12px; border: 1px solid rgba(16, 185, 129, 0.15); font-size: 13px; color: #065f46;">
                                        <strong>Respon Dokter:</strong><br>
                                        {{ $comp->doctor_response }}
                                    </div>
                                @else
                                    <form action="{{ route('dokter.complaints.respond', $comp->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 8px;">
                                        @csrf
                                        <textarea name="doctor_response" class="form-control" style="padding: 8px 12px; font-size: 13px;" placeholder="Tulis instruksi medis/tindakan..." rows="2" required></textarea>
                                        <button type="submit" class="btn btn-primary btn-sm" style="align-self: flex-end;"><i class="ri-check-double-line"></i> Kirim Tanggapan</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 32px;">Keluhan pasca imunisasi tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $complaints->appends(['filter' => request('filter')])->links() }}
        </div>
    </div>
@endsection
