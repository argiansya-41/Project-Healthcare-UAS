@extends('layouts.app')

@section('header-title', 'Kelola Vaksin Imunisasi')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700;">Daftar Vaksin Imunisasi</h3>
            <a href="{{ route('admin.vaccines.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Tambah Vaksin Baru
            </a>
        </div>

        <!-- Search bar -->
        <form action="{{ route('admin.vaccines.index') }}" method="GET" style="display: flex; gap: 16px; margin-bottom: 24px; max-width: 500px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama, kode, atau deskripsi..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary"><i class="ri-search-2-line"></i> Cari</button>
            @if(request('search'))
                <a href="{{ route('admin.vaccines.index') }}" class="btn btn-danger" style="padding: 12px;"><i class="ri-close-line"></i></a>
            @endif
        </form>

        <!-- Vaccines Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th style="width: 150px;">Kode Vaksin</th>
                        <th>Nama Vaksin</th>
                        <th style="width: 150px;">Target Usia</th>
                        <th>Deskripsi</th>
                        <th style="text-align: right; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vaccines as $vac)
                        <tr>
                            <td><code>#{{ $vac->id }}</code></td>
                            <td><span class="badge badge-info" style="background-color: rgba(59, 130, 246, 0.1); color: var(--info); font-family: monospace; font-size: 13px;">{{ $vac->code }}</span></td>
                            <td><strong>{{ $vac->name }}</strong></td>
                            <td>
                                <span class="badge badge-warning" style="background-color: rgba(245, 158, 11, 0.1); color: var(--warning); font-size: 12px; font-weight: 600;">
                                    {{ $vac->target_age_months }} Bulan
                                </span>
                            </td>
                            <td>{{ $vac->description ?? '-' }}</td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('admin.vaccines.edit', $vac->id) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px; display: inline-flex;" title="Edit"><i class="ri-pencil-line"></i></a>
                                
                                <form action="{{ route('admin.vaccines.destroy', $vac->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px; display: inline-flex;" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data vaksin ini?')">
                                        <i class="ri-delete-bin-line" style="pointer-events: none;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 32px;">Data vaksin tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $vaccines->links() }}
        </div>
    </div>
@endsection
