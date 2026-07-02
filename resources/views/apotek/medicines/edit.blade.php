@extends('layouts.app')

@section('header-title', 'Edit Data Obat')

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('apotek.medicines.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
            <h3 style="font-size: 18px; font-weight: 700;">Formulir Edit Obat</h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="ri-error-warning-fill" style="font-size: 20px;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('apotek.medicines.update', $medicine->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="form-group">
                    <label for="code">Kode Obat</label>
                    <input type="text" id="code" name="code" class="form-control" placeholder="OBT-xxx" value="{{ old('code', $medicine->code) }}" required>
                </div>

                <div class="form-group">
                    <label for="name">Nama Obat</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Nama Obat" value="{{ old('name', $medicine->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="category_id">Kategori</label>
                    <select id="category_id" name="category_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $medicine->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="unit_id">Satuan</label>
                    <select id="unit_id" name="unit_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $medicine->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="stock">Stok Saat Ini</label>
                    <input type="number" id="stock" name="stock" class="form-control" placeholder="0" value="{{ old('stock', $medicine->stock) }}" min="0" required>
                </div>

                <div class="form-group">
                    <label for="min_stock">Stok Minimum (Alert)</label>
                    <input type="number" id="min_stock" name="min_stock" class="form-control" placeholder="10" value="{{ old('min_stock', $medicine->min_stock) }}" min="0" required>
                </div>

                <div class="form-group">
                    <label for="purchase_price">Harga Beli (Rp)</label>
                    <input type="number" step="0.01" id="purchase_price" name="purchase_price" class="form-control" placeholder="Harga beli" value="{{ old('purchase_price', $medicine->purchase_price) }}" required>
                </div>

                <div class="form-group">
                    <label for="selling_price">Harga Jual (Rp)</label>
                    <input type="number" step="0.01" id="selling_price" name="selling_price" class="form-control" placeholder="Harga jual" value="{{ old('selling_price', $medicine->selling_price) }}" required>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="expiration_date">Tanggal Kadaluarsa</label>
                    <input type="date" id="expiration_date" name="expiration_date" class="form-control" value="{{ old('expiration_date', $medicine->expiration_date->toDateString()) }}" required>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="description">Deskripsi / Indikasi</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Deskripsi khasiat obat..." rows="3">{{ old('description', $medicine->description) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="grid-column: span 2; justify-content: center; padding: 14px;">
                    <i class="ri-save-line"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
