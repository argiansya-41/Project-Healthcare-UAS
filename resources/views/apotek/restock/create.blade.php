@extends('layouts.app')

@section('header-title', 'Ajukan Restock Obat')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('apotek.restock-requests.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
            <h3 style="font-size: 18px; font-weight: 700;">Formulir Pengajuan Restock</h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="ri-error-warning-fill" style="font-size: 20px;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('apotek.restock-requests.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="medicine_id">Pilih Obat</label>
                <select id="medicine_id" name="medicine_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                    <option value="" disabled selected>Pilih Obat yang stoknya minim</option>
                    @foreach($medicines as $med)
                        <option value="{{ $med->id }}" {{ old('medicine_id') == $med->id ? 'selected' : '' }}>
                            {{ $med->name }} (Stok Saat Ini: {{ $med->stock }} {{ $med->unit->abbreviation }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="quantity">Jumlah Restock yang Diajukan</label>
                <input type="number" id="quantity" name="quantity" class="form-control" placeholder="0" value="{{ old('quantity') }}" min="1" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 14px; margin-top: 10px;">
                <i class="ri-send-plane-line"></i> Kirim Pengajuan Ke Admin
            </button>
        </form>
    </div>
@endsection
