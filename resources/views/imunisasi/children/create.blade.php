@extends('layouts.app')

@section('header-title', 'Daftarkan Anak Baru')

@section('content')
    <div class="card" style="max-width: 750px; margin: 0 auto;">
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('imunisasi.children.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
            <h3 style="font-size: 18px; font-weight: 700;">Formulir Pendaftaran Anak</h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="ri-error-warning-fill" style="font-size: 20px;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('imunisasi.children.store') }}" method="POST">
            @csrf
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="form-group">
                    <label for="name">Nama Lengkap Anak</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Nama Lengkap Anak" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="nik">NIK Anak</label>
                    <input type="text" id="nik" name="nik" class="form-control" placeholder="16 Digit NIK Anak (opsional)" maxlength="16" value="{{ old('nik') }}">
                </div>

                <div class="form-group">
                    <label for="gender">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        <option value="" disabled selected>Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Tanggal Lahir</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" required>
                </div>

                <div class="form-group">
                    <label for="place_of_birth">Tempat Lahir</label>
                    <input type="text" id="place_of_birth" name="place_of_birth" class="form-control" placeholder="Kota / Kabupaten Lahir" value="{{ old('place_of_birth') }}">
                </div>

                <div class="form-group">
                    <label for="birth_weight">Berat Lahir (kg)</label>
                    <input type="number" step="0.01" id="birth_weight" name="birth_weight" class="form-control" placeholder="Contoh: 3.15" value="{{ old('birth_weight') }}">
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="parent_id">Orang Tua / Wali Penanggung Jawab (Akun Warga)</label>
                    <select id="parent_id" name="parent_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        <option value="" disabled selected>Hubungkan ke Akun Warga (Orang Tua)</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }} (NIK: {{ $parent->nik ?? '-' }} | HP: {{ $parent->phone_number ?? '-' }})
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
