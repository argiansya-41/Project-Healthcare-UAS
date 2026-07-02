@extends('layouts.app')

@section('header-title', 'Jadwal & Catatan Imunisasi')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700;">Daftar Pelaksanaan Imunisasi Anak</h3>
            <a href="{{ route('imunisasi.schedules.create') }}" class="btn btn-primary">
                <i class="ri-calendar-add-line"></i> Buat Jadwal Vaksinasi
            </a>
        </div>

        <!-- Filter bar -->
        <form action="{{ route('imunisasi.schedules.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1.5fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div class="form-group" style="margin-bottom: 0;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama anak..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="status" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                    <option value="">Semua Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Dijadwalkan (Scheduled)</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                    <option value="missed" {{ request('status') == 'missed' ? 'selected' : '' }}>Terlewat (Missed)</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-secondary" style="flex-grow: 1; justify-content: center;"><i class="ri-filter-2-line"></i> Filter</button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('imunisasi.schedules.index') }}" class="btn btn-danger" style="padding: 12px;"><i class="ri-close-line"></i></a>
                @endif
            </div>
        </form>

        <!-- Schedules Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Jadwal Vaksin</th>
                        <th>Nama Anak</th>
                        <th>Vaksin Diberikan</th>
                        <th>Tanggal Pelaksanaan</th>
                        <th>Batch Vaksin</th>
                        <th>Petugas</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                        <tr>
                            <td><strong>{{ $rec->scheduled_date->format('d/m/Y') }}</strong></td>
                            <td>
                                <strong>{{ $rec->child->name }}</strong>
                                <br><small style="color: var(--text-secondary)">{{ $rec->child->getAgeMonths() }} Bulan | Orang Tua: {{ $rec->child->parent->name }}</small>
                            </td>
                            <td><span class="badge badge-info">{{ $rec->vaccine->name }}</span><br><small style="color: var(--text-secondary)">Target: {{ $rec->vaccine->target_age_months }} Bulan</small></td>
                            <td>{{ $rec->administered_date ? $rec->administered_date->format('d/m/Y') : '-' }}</td>
                            <td><code>{{ $rec->batch_number ?? '-' }}</code></td>
                            <td>{{ $rec->officer ? $rec->officer->name : '-' }}</td>
                            <td>
                                @if($rec->status === 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($rec->status === 'missed')
                                    <span class="badge badge-danger">Missed</span>
                                @else
                                    <span class="badge badge-warning">Scheduled</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('imunisasi.schedules.edit', $rec->id) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px; display: inline-flex;" title="Update Status / Detail"><i class="ri-pencil-line"></i> Edit</a>
                                <form action="{{ route('imunisasi.schedules.destroy', $rec->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px; display: inline-flex;" onclick="return confirm('Apakah Anda yakin ingin menghapus catatan imunisasi ini?')">
                                        <i class="ri-delete-bin-line" style="pointer-events: none;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 32px;">Catatan pelaksanaan imunisasi tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $records->links() }}
        </div>
    </div>
@endsection
