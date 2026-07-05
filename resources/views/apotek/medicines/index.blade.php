@extends('layouts.app')

@section('header-title', 'Stok & Inventaris Obat')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700;">Daftar Obat Puskesmas</h3>
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('apotek.medicines.create') }}" class="btn btn-primary">
                    <i class="ri-add-line"></i> Tambah Obat Baru
                </a>
            </div>
        </div>

        <!-- Filter bar -->
        <form action="{{ route('apotek.medicines.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 24px;">
            <div class="form-group" style="margin-bottom: 0;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama atau kode obat..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="category_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <select name="filter" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                    <option value="">Semua Kondisi</option>
                    <option value="low_stock" {{ request('filter') == 'low_stock' ? 'selected' : '' }}>Stok Hampir Habis</option>
                    <option value="expired" {{ request('filter') == 'expired' ? 'selected' : '' }}>Sudah Kadaluarsa</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-secondary" style="flex-grow: 1; justify-content: center;"><i class="ri-search-2-line"></i> Cari</button>
                @if(request()->anyFilled(['search', 'category_id', 'filter']))
                    <a href="{{ route('apotek.medicines.index') }}" class="btn btn-danger" style="padding: 12px;"><i class="ri-close-line"></i></a>
                @endif
            </div>
        </form>

        <!-- Medicines Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode Obat</th>
                        <th>Nama Obat</th>
                        <th>Kategori</th>
                        <th>Harga Jual (Beli)</th>
                        <th>Stok / Batas</th>
                        <th>Kadaluarsa</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicines as $med)
                        <tr>
                            <td><code>{{ $med->code }}</code></td>
                            <td><strong>{{ $med->name }}</strong></td>
                            <td>{{ $med->category->name }}</td>
                            <td>
                                Rp{{ number_format($med->selling_price, 0, ',', '.') }}
                                <br><small style="color: var(--text-secondary)">Beli: Rp{{ number_format($med->purchase_price, 0, ',', '.') }}</small>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 6px;">
                                    <div style="display: flex; align-items: baseline; gap: 4px;">
                                        <strong style="font-size: 15px; font-weight: 700; color: var(--text-primary);">{{ $med->stock }}</strong>
                                        <span style="font-size: 11px; color: var(--text-secondary);">/ {{ $med->min_stock }} {{ $med->unit->abbreviation }}</span>
                                    </div>
                                    @php
                                        // Max represents a healthy filled state, e.g. min_stock * 2 or current stock
                                        $maxVal = max($med->min_stock * 2, $med->stock, 10);
                                        $percent = ($med->stock / $maxVal) * 100;
                                        
                                        // Color logic matching user criteria
                                        if ($med->stock <= $med->min_stock) {
                                            $barColor = 'var(--danger)';
                                        } elseif ($med->stock <= $med->min_stock * 1.5) {
                                            $barColor = 'var(--warning)';
                                        } else {
                                            $barColor = 'var(--success)';
                                        }
                                    @endphp
                                    <div style="width: 100%; max-width: 120px; height: 6px; background-color: #f1f5f9; border-radius: 9999px; overflow: hidden; display: flex; border: 1px solid var(--card-border);">
                                        <div style="width: {{ $percent }}%; height: 100%; background-color: {{ $barColor }}; border-radius: 9999px; transition: width 0.3s ease;"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="color: {{ $med->isExpired() ? 'var(--danger)' : 'inherit' }}">
                                    {{ $med->expiration_date->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>
                                @if($med->isExpired())
                                    <span class="badge badge-danger">Kadaluarsa</span>
                                @elseif($med->isAlmostOutOfStock())
                                    <span class="badge badge-warning">Stok Rendah</span>
                                @else
                                    <span class="badge badge-success">Aman</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('apotek.medicines.edit', $med->id) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px; display: inline-flex;"><i class="ri-pencil-line"></i></a>
                                
                                <form action="{{ route('apotek.medicines.destroy', $med->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px; display: inline-flex;" onclick="return confirm('Apakah Anda yakin ingin menghapus obat ini?')">
                                        <i class="ri-delete-bin-line" style="pointer-events: none;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 32px;">Obat tidak ditemukan.</td>
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
