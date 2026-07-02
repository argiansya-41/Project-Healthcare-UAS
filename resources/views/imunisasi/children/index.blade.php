@extends('layouts.app')

@section('header-title', 'Pendaftaran Anak & Bayi')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700;">Daftar Anak / Penerima Imunisasi</h3>
            <a href="{{ route('imunisasi.children.create') }}" class="btn btn-primary">
                <i class="ri-user-add-line"></i> Daftarkan Anak Baru
            </a>
        </div>

        <!-- Search bar -->
        <form action="{{ route('imunisasi.children.index') }}" method="GET" style="display: flex; gap: 16px; margin-bottom: 24px; max-width: 500px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama anak atau NIK..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary"><i class="ri-search-2-line"></i> Cari</button>
            @if(request('search'))
                <a href="{{ route('imunisasi.children.index') }}" class="btn btn-danger" style="padding: 12px;"><i class="ri-close-line"></i></a>
            @endif
        </form>

        <!-- Children Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama Anak</th>
                        <th>Jenis Kelamin</th>
                        <th>Tgl Lahir / Usia</th>
                        <th>Tempat Lahir / Berat</th>
                        <th>Orang Tua (Parent)</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($children as $ch)
                        <tr>
                            <td><code>{{ $ch->nik ?? '-' }}</code></td>
                            <td><strong>{{ $ch->name }}</strong></td>
                            <td>{{ $ch->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td>
                                {{ $ch->date_of_birth->format('d/m/Y') }}
                                <br><small style="color: var(--text-secondary)">{{ $ch->getAgeMonths() }} Bulan</small>
                            </td>
                            <td>
                                {{ $ch->place_of_birth ?? '-' }}
                                <br><small style="color: var(--text-secondary)">{{ $ch->birth_weight ?? '-' }} kg</small>
                            </td>
                            <td>
                                {{ $ch->parent->name }}
                                <br><small style="color: var(--text-secondary)">HP: {{ $ch->parent->phone_number ?? '-' }}</small>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('imunisasi.schedules.create', ['child_id' => $ch->id]) }}" class="btn btn-primary btn-sm" style="padding: 6px 10px; display: inline-flex;" title="Jadwalkan Vaksin"><i class="ri-calendar-add-line"></i></a>
                                <a href="{{ route('imunisasi.children.edit', $ch->id) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px; display: inline-flex;"><i class="ri-pencil-line"></i></a>
                                <form action="{{ route('imunisasi.children.destroy', $ch->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px; display: inline-flex;" onclick="return confirm('Apakah Anda yakin ingin menghapus data anak ini?')">
                                        <i class="ri-delete-bin-line" style="pointer-events: none;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 32px;">Anak tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $children->links() }}
        </div>
    </div>
@endsection
