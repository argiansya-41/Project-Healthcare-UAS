@extends('layouts.app')

@section('header-title', 'Daftar Obat Puskesmas')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <div>
                <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">Daftar Ketersediaan Obat</h3>
                <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Informasi ketersediaan obat secara real-time di Puskesmas.</p>
            </div>
        </div>

        <!-- Filter bar -->
        <form action="{{ route('warga.medicines.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1.5fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div class="form-group" style="margin-bottom: 0;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama atau deskripsi obat..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="category_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-secondary" style="flex-grow: 1; justify-content: center;"><i class="ri-search-2-line"></i> Cari</button>
                @if(request()->anyFilled(['search', 'category_id']))
                    <a href="{{ route('warga.medicines.index') }}" class="btn btn-danger" style="padding: 12px;"><i class="ri-close-line"></i></a>
                @endif
            </div>
        </form>

        <!-- Medicines Table -->
        <div class="table-responsive" style="margin-top: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 120px;">Kode</th>
                        <th>Nama Obat</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th style="width: 180px; text-align: center;">Status Ketersediaan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicines as $med)
                        <tr>
                            <td><span style="font-size: 12px; font-family: monospace; font-weight: 600; padding: 4px 8px; background: #f1f5f9; border-radius: 6px; color: var(--text-secondary);">{{ $med->code }}</span></td>
                            <td><strong style="color: var(--text-primary); font-size: 14px;">{{ $med->name }}</strong></td>
                            <td><span class="badge badge-info">{{ $med->category->name }}</span></td>
                            <td style="color: var(--text-secondary); line-height: 1.5; font-size: 13px;">{{ $med->description ?? '-' }}</td>
                            <td style="text-align: center;">
                                @if($med->stock <= 0)
                                    <span class="badge badge-danger">Habis</span>
                                @elseif($med->stock <= $med->min_stock)
                                    <span class="badge badge-warning">Stok Terbatas</span>
                                @else
                                    <span class="badge badge-success">Tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 32px;">
                                <i class="ri-capsule-line" style="font-size: 32px; color: var(--text-secondary); opacity: 0.5; display: block; margin-bottom: 8px;"></i>
                                Obat tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $medicines->links() }}
        </div>
    </div>
@endsection
