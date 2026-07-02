@extends('layouts.app')

@section('header-title', 'Edit Data Anak')

@section('content')
    <div class="card" style="max-width: 750px; margin: 0 auto;">
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('imunisasi.children.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
            <h3 style="font-size: 18px; font-weight: 700;">Formulir Edit Data Anak</h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="ri-error-warning-fill" style="font-size: 20px;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('imunisasi.children.update', $child->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="form-group">
                    <label for="name">Nama Lengkap Anak</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Nama Lengkap Anak" value="{{ old('name', $child->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="nik">NIK Anak</label>
                    <input type="text" id="nik" name="nik" class="form-control" placeholder="16 Digit NIK" maxlength="16" value="{{ old('nik', $child->nik) }}">
                </div>

                <div class="form-group">
                    <label for="gender">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        <option value="L" {{ old('gender', $child->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $child->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Tanggal Lahir</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $child->date_of_birth->toDateString()) }}" required>
                </div>

                <div class="form-group">
                    <label for="place_of_birth">Tempat Lahir</label>
                    <input type="text" id="place_of_birth" name="place_of_birth" class="form-control" placeholder="Kota Lahir" value="{{ old('place_of_birth', $child->place_of_birth) }}">
                </div>

                <div class="form-group">
                    <label for="birth_weight">Berat Lahir (kg)</label>
                    <input type="number" step="0.01" id="birth_weight" name="birth_weight" class="form-control" placeholder="Berat lahir" value="{{ old('birth_weight', $child->birth_weight) }}">
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="parent_id">Orang Tua / Wali Penanggung Jawab (Akun Warga)</label>
                    <select id="parent_id" name="parent_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $child->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }} (NIK: {{ $parent->nik ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="grid-column: span 2; justify-content: center; padding: 14px; margin-top: 10px;">
                    <i class="ri-save-line"></i> Simpan Data Anak
                </button>
            </div>
        </form>
    </div>
@endsection
