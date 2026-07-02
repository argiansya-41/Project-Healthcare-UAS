@extends('layouts.app')

@section('header-title', 'Tambah Vaksin Imunisasi')

@section('content')
    <div class="card" style="max-width: 650px; margin: 0 auto;">
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('admin.vaccines.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
            <h3 style="font-size: 18px; font-weight: 700;">Formulir Tambah Vaksin Baru</h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="ri-error-warning-fill" style="font-size: 20px;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('admin.vaccines.store') }}" method="POST">
            @csrf
            
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="form-group">
                    <label for="code">Kode Vaksin (Singkat, Unik)</label>
                    <input type="text" id="code" name="code" class="form-control" placeholder="Contoh: HB-0, BCG, DPT-HB-Hib-1" value="{{ old('code') }}" required>
                </div>

                <div class="form-group">
                    <label for="name">Nama Vaksin</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Contoh: Hepatitis B (HB-O)" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="target_age_months">Target Usia Pemberian (Bulan)</label>
                    <input type="number" id="target_age_months" name="target_age_months" class="form-control" placeholder="Contoh: 0 untuk baru lahir, 1, 2, 9, dst." min="0" value="{{ old('target_age_months', 0) }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi Vaksin / Catatan Efek Samping</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Keterangan singkat mengenai fungsi vaksin atau rekomendasi klinis..." rows="4">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="justify-content: center; padding: 14px; margin-top: 10px;">
                    <i class="ri-save-line"></i> Simpan Vaksin Baru
                </button>
            </div>
        </form>
    </div>
@endsection
