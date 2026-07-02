@extends('layouts.app')

@section('header-title', 'Log Transaksi Obat')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; margin: 0;">Log Transaksi Masuk / Keluar</h3>
            @if($transactions->total() > 0)
                <form action="{{ route('apotek.transactions.clearAll') }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin membersihkan semua riwayat transaksi stok obat?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-delete-bin-7-line"></i> Bersihkan Semua
                    </button>
                </form>
            @endif
        </div>

        <!-- Filter bar -->
        <form action="{{ route('apotek.transactions.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1.5fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div class="form-group" style="margin-bottom: 0;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama obat..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="type" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                    <option value="">Semua Tipe</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Masuk (In)</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Keluar (Out)</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-secondary" style="flex-grow: 1; justify-content: center;"><i class="ri-filter-2-line"></i> Filter</button>
                @if(request()->anyFilled(['search', 'type']))
                    <a href="{{ route('apotek.transactions.index') }}" class="btn btn-danger" style="padding: 12px;"><i class="ri-close-line"></i></a>
                @endif
            </div>
        </form>

        <!-- Transactions Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal Transaksi</th>
                        <th>Obat</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Pemasok (Supplier)</th>
                        <th>Operator Catat</th>
                        <th>Catatan</th>
                        <th style="width: 80px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td><strong>{{ $tx->transaction_date->format('d/m/Y') }}</strong></td>
                            <td>
                                <strong>{{ $tx->medicine->name }}</strong>
                                <br><small style="color: var(--text-secondary)">{{ $tx->medicine->code }}</small>
                            </td>
                            <td>
                                @if($tx->type === 'in')
                                    <span class="badge badge-success">Masuk (In)</span>
                                @else
                                    <span class="badge badge-danger">Keluar (Out)</span>
                                @endif
                            </td>
                            <td><strong>{{ $tx->quantity }} {{ $tx->medicine->unit->abbreviation }}</strong></td>
                            <td>{{ $tx->supplier ? $tx->supplier->name : '-' }}</td>
                            <td>{{ $tx->user->name }} <br><small style="color: var(--text-secondary)">{{ ucfirst($tx->user->role) }}</small></td>
                            <td>{{ $tx->notes ?? '-' }}</td>
                            <td style="text-align: center;">
                                <form action="{{ route('apotek.transactions.destroy', $tx->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px; display: inline-flex;" onclick="return confirm('Apakah Anda yakin ingin menghapus data transaksi ini?')">
                                        <i class="ri-delete-bin-7-line" style="pointer-events: none;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 32px;">Transaksi belum tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
