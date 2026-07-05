@extends('layouts.app')

@section('header-title', 'Input Transaksi Obat')

@section('styles')
<style>
    .transaction-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    @media (max-width: 768px) {
        .transaction-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .transaction-grid > .form-group,
        .transaction-grid > button {
            grid-column: span 1 !important;
        }
    }
</style>
@endsection

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div style="margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; margin: 0;">Formulir Input Transaksi Obat</h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="ri-error-warning-fill" style="font-size: 20px;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('apotek.transactions.store') }}" method="POST">
            @csrf
            <div class="transaction-grid">
                <div class="form-group">
                    <label for="medicine_id">Pilih Obat</label>
                    <select id="medicine_id" name="medicine_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        <option value="" disabled selected>Pilih Obat</option>
                        @foreach($medicines as $med)
                            <option value="{{ $med->id }}" {{ old('medicine_id') == $med->id ? 'selected' : '' }}>
                                {{ $med->name }} (Stok: {{ $med->stock }} {{ $med->unit->abbreviation }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="type">Tipe Transaksi</label>
                    <select id="type" name="type" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required onchange="toggleSupplier()">
                        <option value="" disabled selected>Pilih Tipe</option>
                        <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Stok Masuk (Barang Masuk / Pembelian)</option>
                        <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Stok Keluar (Pemberian Resep / Penggunaan)</option>
                    </select>
                </div>

                <div class="form-group" id="supplier-group">
                    <label for="supplier_id">Pemasok (Supplier)</label>
                    <select id="supplier_id" name="supplier_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                        <option value="" selected>Pilih Pemasok (Opsional)</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Jumlah Transaksi</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" placeholder="0" value="{{ old('quantity') }}" min="1" required>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="transaction_date">Tanggal Transaksi</label>
                    <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="{{ old('transaction_date', now()->toDateString()) }}" required>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="notes">Catatan / Keterangan</label>
                    <textarea id="notes" name="notes" class="form-control" placeholder="Contoh: Penggunaan resep Dokter untuk pasien X, atau Pembelian pasokan Kimia Farma..." rows="3">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="grid-column: span 2; justify-content: center; padding: 14px;">
                    <i class="ri-save-line"></i> Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    function toggleSupplier() {
        const type = document.getElementById('type').value;
        const supplierGroup = document.getElementById('supplier-group');
        if (type === 'out') {
            supplierGroup.style.display = 'none';
        } else {
            supplierGroup.style.display = 'block';
        }
    }
    document.addEventListener("DOMContentLoaded", toggleSupplier);
</script>
@endsection
