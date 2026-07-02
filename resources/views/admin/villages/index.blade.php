@extends('layouts.app')

@section('header-title', 'Kelola Wilayah / Desa')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700;">Daftar Wilayah Puskesmas</h3>
            <a href="{{ route('admin.villages.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Tambah Wilayah Baru
            </a>
        </div>

        <!-- Search bar -->
        <form action="{{ route('admin.villages.index') }}" method="GET" style="display: flex; gap: 16px; margin-bottom: 24px; max-width: 500px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama desa, kecamatan, kabupaten..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary"><i class="ri-search-2-line"></i> Cari</button>
            @if(request('search'))
                <a href="{{ route('admin.villages.index') }}" class="btn btn-danger" style="padding: 12px;"><i class="ri-close-line"></i></a>
            @endif
        </form>

        <!-- Villages Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Desa / Kelurahan</th>
                        <th>Kecamatan</th>
                        <th>Kabupaten</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($villages as $v)
                        <tr>
                            <td><code>#{{ $v->id }}</code></td>
                            <td><strong>{{ $v->name }}</strong></td>
                            <td>{{ $v->kecamatan }}</td>
                            <td>{{ $v->kabupaten }}</td>
                            <td><code>{{ $v->latitude }}</code></td>
                            <td><code>{{ $v->longitude }}</code></td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('admin.villages.edit', $v->id) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px; display: inline-flex;"><i class="ri-pencil-line"></i></a>
                                
                                <form action="{{ route('admin.villages.destroy', $v->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px; display: inline-flex;" onclick="return confirm('Apakah Anda yakin ingin menghapus data wilayah ini?')">
                                        <i class="ri-delete-bin-line" style="pointer-events: none;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 32px;">Data wilayah/desa tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $villages->links() }}
        </div>
    </div>
@endsection
