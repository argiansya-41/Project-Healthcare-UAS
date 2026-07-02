@extends('layouts.app')

@section('header-title', 'Laporkan Kasus Penyakit')

@section('content')
    <div class="card" style="max-width: 850px; margin: 0 auto;">
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('warga.reports.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
            <h3 style="font-size: 18px; font-weight: 700;">Formulir Pelaporan Penyakit Mandiri</h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="ri-error-warning-fill" style="font-size: 20px;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('warga.reports.store') }}" method="POST">
            @csrf
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <h4 style="grid-column: span 2; font-size: 15px; font-weight: 700; border-bottom: 1px solid var(--card-border); padding-bottom: 8px; margin-top: 10px; color: var(--accent-color);">
                    <i class="ri-user-line"></i> Identitas Pasien
                </h4>

                <div class="form-group">
                    <label for="patient_name">Nama Lengkap Pasien</label>
                    <input type="text" id="patient_name" name="patient_name" class="form-control" placeholder="Nama Pasien" value="{{ old('patient_name') }}" required>
                </div>

                <div class="form-group">
                    <label for="patient_nik">NIK Pasien</label>
                    <input type="text" id="patient_nik" name="patient_nik" class="form-control" placeholder="16 Digit NIK" maxlength="16" value="{{ old('patient_nik') }}" required>
                </div>

                <div class="form-group">
                    <label for="patient_age">Umur Pasien (Tahun)</label>
                    <input type="number" id="patient_age" name="patient_age" class="form-control" placeholder="Umur" value="{{ old('patient_age') }}" min="0" required>
                </div>

                <div class="form-group">
                    <label for="patient_gender">Jenis Kelamin</label>
                    <select id="patient_gender" name="patient_gender" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        <option value="" disabled selected>Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('patient_gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('patient_gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="patient_address">Alamat Tempat Tinggal Pasien</label>
                    <input type="text" id="patient_address" name="patient_address" class="form-control" placeholder="Alamat lengkap domisili saat ini" value="{{ old('patient_address') }}" required>
                </div>

                <h4 style="grid-column: span 2; font-size: 15px; font-weight: 700; border-bottom: 1px solid var(--card-border); padding-bottom: 8px; margin-top: 20px; color: var(--accent-color);">
                    <i class="ri-microscope-line"></i> Gejala & Lokasi
                </h4>

                <div class="form-group">
                    <label for="disease_type_id">Jenis Penyakit (Diagnosa Awal)</label>
                    <select id="disease_type_id" name="disease_type_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        <option value="" disabled selected>Pilih Diagnosa Penyakit</option>
                        @foreach($diseaseTypes as $type)
                            <option value="{{ $type->id }}" {{ old('disease_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }} ({{ $type->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="severity">Tingkat Keparahan Gejala</label>
                    <select id="severity" name="severity" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        <option value="" disabled selected>Pilih Keparahan</option>
                        <option value="ringan" {{ old('severity') == 'ringan' ? 'selected' : '' }}>Ringan (Gejala Terkontrol)</option>
                        <option value="sedang" {{ old('severity') == 'sedang' ? 'selected' : '' }}>Sedang (Butuh Pengawasan Medis)</option>
                        <option value="berat" {{ old('severity') == 'berat' ? 'selected' : '' }}>Berat (Butuh Penanganan Intensif)</option>
                    </select>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="report_date">Tanggal Mulai Sakit</label>
                    <input type="date" id="report_date" name="report_date" class="form-control" value="{{ old('report_date', now()->toDateString()) }}" required>
                </div>

                <!-- GIS Coordinates (Villages Dropdown) -->
                <div class="form-group" style="grid-column: span 2;">
                    <label for="village_id">Pilih Wilayah Domisili (Desa, Kecamatan, Kabupaten)</label>
                    <select id="village_id" name="village_id" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%2364748b\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/><path d=\'M0 0h24v24H0z\' fill=\'none\'/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;" required>
                        <option value="" disabled selected>Pilih Desa / Kelurahan</option>
                        @foreach($villages as $v)
                            <option value="{{ $v->id }}" 
                                    data-lat="{{ $v->latitude }}" 
                                    data-lng="{{ $v->longitude }}"
                                    data-kecamatan="{{ $v->kecamatan }}"
                                    data-kabupaten="{{ $v->kabupaten }}"
                                    {{ old('village_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->name }} (Kec. {{ $v->kecamatan }}, {{ $v->kabupaten }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="kecamatan_display">Kecamatan</label>
                    <input type="text" id="kecamatan_display" class="form-control" placeholder="Pilih wilayah..." readonly style="background-color: rgba(255,255,255,0.05); cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label for="kabupaten_display">Kabupaten / Kota</label>
                    <input type="text" id="kabupaten_display" class="form-control" placeholder="Pilih wilayah..." readonly style="background-color: rgba(255,255,255,0.05); cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label for="latitude">Garis Lintang (Latitude)</label>
                    <input type="text" id="latitude" name="latitude" class="form-control" placeholder="Pilih wilayah..." value="{{ old('latitude') }}" readonly style="background-color: rgba(255,255,255,0.05); cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label for="longitude">Garis Bujur (Longitude)</label>
                    <input type="text" id="longitude" name="longitude" class="form-control" placeholder="Pilih wilayah..." value="{{ old('longitude') }}" readonly style="background-color: rgba(255,255,255,0.05); cursor: not-allowed;">
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="symptoms">Rincian Gejala yang Dirasakan</label>
                    <textarea id="symptoms" name="symptoms" class="form-control" placeholder="Sebutkan gejala klinis yang nampak..." rows="3" required>{{ old('symptoms') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="grid-column: span 2; justify-content: center; padding: 14px; margin-top: 10px;">
                    <i class="ri-send-plane-fill"></i> Kirim Laporan Kasus
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const villageSelect = document.getElementById('village_id');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const kecInput = document.getElementById('kecamatan_display');
        const kabInput = document.getElementById('kabupaten_display');

        function updateLocationFields() {
            const selectedOption = villageSelect.options[villageSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                latInput.value = selectedOption.getAttribute('data-lat') || '';
                lngInput.value = selectedOption.getAttribute('data-lng') || '';
                kecInput.value = selectedOption.getAttribute('data-kecamatan') || '';
                kabInput.value = selectedOption.getAttribute('data-kabupaten') || '';
            } else {
                latInput.value = '';
                lngInput.value = '';
                kecInput.value = '';
                kabInput.value = '';
            }
        }

        villageSelect.addEventListener('change', updateLocationFields);

        if (villageSelect.value) {
            updateLocationFields();
        }
    });
</script>
@endsection
