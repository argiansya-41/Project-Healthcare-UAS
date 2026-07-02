@extends('layouts.app')

@section('header-title', 'Kelola Jenis Penyakit')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700;">Daftar Diagnosa Jenis Penyakit</h3>
            <a href="{{ route('admin.disease-types.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Tambah Penyakit Baru
            </a>
        </div>

        <!-- Search bar -->
        <form action="{{ route('admin.disease-types.index') }}" method="GET" style="display: flex; gap: 16px; margin-bottom: 24px; max-width: 500px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama, kode, atau deskripsi..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary"><i class="ri-search-2-line"></i> Cari</button>
            @if(request('search'))
                <a href="{{ route('admin.disease-types.index') }}" class="btn btn-danger" style="padding: 12px;"><i class="ri-close-line"></i></a>
            @endif
        </form>

        <!-- Disease Types Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th style="width: 150px;">Kode Penyakit</th>
                        <th>Nama Diagnosa Penyakit</th>
                        <th>Deskripsi Medis</th>
                        <th style="text-align: right; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($diseaseTypes as $dt)
                        <tr>
                            <td><code>#{{ $dt->id }}</code></td>
                            <td><span class="badge badge-info" style="background-color: rgba(59, 130, 246, 0.1); color: var(--info); font-family: monospace; font-size: 13px;">{{ $dt->code }}</span></td>
                            <td><strong>{{ $dt->name }}</strong></td>
                            <td>{{ $dt->description ?? '-' }}</td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('admin.disease-types.edit', $dt->id) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px; display: inline-flex;" title="Edit"><i class="ri-pencil-line"></i></a>
                                
                                <form action="{{ route('admin.disease-types.destroy', $dt->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px; display: inline-flex;" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data penyakit ini?')">
                                        <i class="ri-delete-bin-line" style="pointer-events: none;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 32px;">Data jenis penyakit tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $diseaseTypes->links() }}
        </div>
    </div>
@endsection
