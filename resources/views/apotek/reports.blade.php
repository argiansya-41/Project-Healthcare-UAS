@extends('layouts.app')

@section('header-title', 'Laporan Stok Obat')

@section('content')
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700;">Rekapitulasi Stok Obat Puskesmas</h3>
            <a href="{{ route('kepala.reports.export', ['module' => 'obat', 'format' => 'print']) }}" target="_blank" class="btn btn-primary">
                <i class="ri-printer-line"></i> Cetak Laporan Stok (Print)
            </a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode Obat</th>
                        <th>Nama Obat</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Kadaluarsa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicines as $med)
                        <tr>
                            <td><code>{{ $med->code }}</code></td>
                            <td><strong>{{ $med->name }}</strong></td>
                            <td>{{ $med->category->name }}</td>
                            <td>Rp{{ number_format($med->purchase_price, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($med->selling_price, 0, ',', '.') }}</td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 6px;">
                                    <div style="display: flex; align-items: baseline; gap: 4px;">
                                        <strong style="font-size: 14px; font-weight: 700; color: var(--text-primary);">{{ $med->stock }}</strong>
                                        <span style="font-size: 11px; color: var(--text-secondary);">/ {{ $med->min_stock }} {{ $med->unit->abbreviation }}</span>
                                    </div>
                                    @php
                                        $maxVal = max($med->min_stock * 2, $med->stock, 10);
                                        $percent = ($med->stock / $maxVal) * 100;
                                        if ($med->stock <= $med->min_stock) {
                                            $barColor = 'var(--danger)';
                                        } elseif ($med->stock <= $med->min_stock * 1.5) {
                                            $barColor = 'var(--warning)';
                                        } else {
                                            $barColor = 'var(--success)';
                                        }
                                    @endphp
                                    <div style="width: 100%; max-width: 100px; height: 6px; background-color: #f1f5f9; border-radius: 9999px; overflow: hidden; display: flex; border: 1px solid var(--card-border);">
                                        <div style="width: {{ $percent }}%; height: 100%; background-color: {{ $barColor }}; border-radius: 9999px;"></div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $med->expiration_date->format('d/m/Y') }}</td>
                            <td>
                                @if($med->isExpired())
                                    <span class="badge badge-danger">Kadaluarsa</span>
                                @elseif($med->isAlmostOutOfStock())
                                    <span class="badge badge-warning">Stok Rendah</span>
                                @else
                                    <span class="badge badge-success">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
