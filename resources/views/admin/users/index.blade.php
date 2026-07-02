@extends('layouts.app')

@section('header-title', 'Manajemen Pengguna')

@section('content')
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <h3 style="font-size: 18px; font-weight: 700;">Daftar Pengguna Sistem</h3>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="ri-user-add-line"></i> Tambah User Baru
            </a>
        </div>

        <!-- Filter & Search Form -->
        <form action="{{ route('admin.users.index') }}" method="GET" style="margin-top: 24px; display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px;">
            <div class="form-group" style="margin-bottom: 0;">
                <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama, email, NIK..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="role" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="apoteker" {{ request('role') == 'apoteker' ? 'selected' : '' }}>Petugas Apotek</option>
                    <option value="petugas_medis" {{ request('role') == 'petugas_medis' ? 'selected' : '' }}>Petugas Medis</option>
                    <option value="warga" {{ request('role') == 'warga' ? 'selected' : '' }}>Warga / Pasien</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-secondary" style="flex-grow: 1; justify-content: center;"><i class="ri-filter-2-line"></i> Filter</button>
                @if(request()->anyFilled(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-danger" style="justify-content: center; aspect-ratio: 1; padding: 12px;"><i class="ri-close-line"></i></a>
                @endif
            </div>
        </form>

        <!-- Users Table -->
        <div class="table-responsive" style="margin-top: 24px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>NIK / No. HP</th>
                        <th>Role</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $usr)
                        <tr>
                            <td>
                                <strong>{{ $usr->name }}</strong>
                                <br><small style="color: var(--text-secondary)">{{ $usr->gender == 'L' ? 'Laki-laki' : ($usr->gender == 'P' ? 'Perempuan' : '-') }}</small>
                            </td>
                            <td>{{ $usr->email }}</td>
                            <td>
                                <strong>NIK:</strong> {{ $usr->nik ?? '-' }}
                                <br><small style="color: var(--text-secondary)"><strong>HP:</strong> {{ $usr->phone_number ?? '-' }}</small>
                            </td>
                            <td>
                                @if($usr->role === 'admin')
                                    <span class="badge badge-info">Admin</span>
                                @elseif($usr->role === 'apoteker')
                                    <span class="badge badge-success">Apoteker</span>
                                @elseif($usr->role === 'petugas_medis')
                                    <span class="badge badge-warning">Petugas Medis</span>
                                @elseif($usr->role === 'warga')
                                    <span class="badge badge-secondary" style="background-color: #f1f5f9; color: var(--text-secondary);">Warga</span>
                                @else
                                    <span class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $usr->role)) }}</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('admin.users.edit', $usr->id) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px; display: inline-flex;"><i class="ri-pencil-line"></i></a>
                                
                                <form action="{{ route('admin.users.destroy', $usr->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px; display: inline-flex;" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                        <i class="ri-delete-bin-line" style="pointer-events: none;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 32px;">Pengguna tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="margin-top: 24px;">
            {{ $users->links() }}
        </div>
    </div>
@endsection
