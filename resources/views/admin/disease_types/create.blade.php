@extends('layouts.app')

@section('header-title', 'Tambah Jenis Penyakit')

@section('content')
    <div class="card" style="max-width: 650px; margin: 0 auto;">
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('admin.disease-types.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
            <h3 style="font-size: 18px; font-weight: 700;">Formulir Tambah Jenis Penyakit</h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="ri-error-warning-fill" style="font-size: 20px;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('admin.disease-types.store') }}" method="POST">
            @csrf
            
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="form-group">
                    <label for="code">Kode Penyakit (singkat, kapital)</label>
                    <input type="text" id="code" name="code" class="form-control" placeholder="Contoh: DBD, MAL, TBC" value="{{ old('code') }}" required style="text-transform: uppercase;">
                </div>

                <div class="form-group">
                    <label for="name">Nama Diagnosa Penyakit</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Contoh: Malaria Falciparum" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi Medis / Keterangan</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Keterangan klinis singkat mengenai penularan, gejala umum atau kriteria diagnosis..." rows="4">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="justify-content: center; padding: 14px; margin-top: 10px;">
                    <i class="ri-save-line"></i> Simpan Penyakit Baru
                </button>
            </div>
        </form>
    </div>
@endsection
