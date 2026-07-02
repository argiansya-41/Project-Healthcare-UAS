@extends('layouts.app')

@section('header-title', 'Tambah Pengguna Baru')

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
            <h3 style="font-size: 18px; font-weight: 700;">Formulir Tambah User</h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="ri-error-warning-fill" style="font-size: 20px;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="role">Role / Peran</label>
                    <select id="role" name="role" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        <option value="" disabled selected>Pilih Role</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="apoteker" {{ old('role') == 'apoteker' ? 'selected' : '' }}>Petugas Apotek (Apoteker)</option>
                        <option value="petugas_medis" {{ old('role') == 'petugas_medis' ? 'selected' : '' }}>Petugas Medis</option>
                        <option value="warga" {{ old('role') == 'warga' ? 'selected' : '' }}>Warga / Pasien</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nik">NIK (Nomor Induk Kependudukan)</label>
                    <input type="text" id="nik" name="nik" class="form-control" placeholder="16 Digit NIK (opsional untuk staf)" maxlength="16" value="{{ old('nik') }}">
                </div>

                <div class="form-group">
                    <label for="phone_number">No. Handphone / WhatsApp</label>
                    <input type="text" id="phone_number" name="phone_number" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('phone_number') }}">
                </div>

                <div class="form-group">
                    <label for="gender">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                        <option value="" disabled selected>Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="address">Alamat Domisili</label>
                    <input type="text" id="address" name="address" class="form-control" placeholder="Alamat lengkap" value="{{ old('address') }}">
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="password">Password Default</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password untuk login awal" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="grid-column: span 2; justify-content: center; padding: 14px; margin-top: 10px;">
                    <i class="ri-save-line"></i> Simpan User Baru
                </button>
            </div>
        </form>
    </div>
@endsection
