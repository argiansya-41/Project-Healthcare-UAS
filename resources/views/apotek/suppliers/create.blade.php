@extends('layouts.app')

@section('header-title', 'Tambah Supplier')

@section('content')
<div class="card">
    <h3 class="text-xl font-semibold mb-4">Tambah Supplier Baru</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="ri-error-warning-fill"></i>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('apotek.suppliers.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="form-group">
            <label for="name">Nama Supplier</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label for="contact_name">Nama Kontak</label>
            <input type="text" name="contact_name" id="contact_name" class="form-control" value="{{ old('contact_name') }}" required>
        </div>
        <div class="form-group">
            <label for="phone">Telepon</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" required>
        </div>
        <div class="form-group">
            <label for="address">Alamat</label>
            <textarea name="address" id="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>
        </div>
        <div class="flex justify-end space-x-3">
            <a href="{{ route('apotek.suppliers.index') }}" class="btn btn-secondary"><i class="ri-arrow-left-line"></i> Kembali</a>
            <button type="submit" class="btn btn-primary"><i class="ri-check-line"></i> Simpan</button>
        </div>
    </form>
</div>
@endsection
