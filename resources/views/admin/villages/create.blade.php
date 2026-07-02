@extends('layouts.app')

@section('header-title', 'Tambah Wilayah Baru')

@section('content')
    <div class="card" style="max-width: 650px; margin: 0 auto;">
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('admin.villages.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;"><i class="ri-arrow-left-line"></i> Kembali</a>
            <h3 style="font-size: 18px; font-weight: 700;">Formulir Tambah Wilayah/Desa</h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="ri-error-warning-fill" style="font-size: 20px;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('admin.villages.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="name">Nama Desa / Kelurahan</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Contoh: Kedoya Utara" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="kecamatan">Kecamatan</label>
                <input type="text" id="kecamatan" name="kecamatan" class="form-control" placeholder="Contoh: Kebon Jeruk" value="{{ old('kecamatan') }}" required>
            </div>

            <div class="form-group">
                <label for="kabupaten">Kabupaten / Kota</label>
                <input type="text" id="kabupaten" name="kabupaten" class="form-control" placeholder="Contoh: Jakarta Barat" value="{{ old('kabupaten') }}" required>
            </div>

            <div style="margin-bottom: 24px;">
                <button type="button" id="btn-lookup-coordinates" class="btn btn-secondary" style="width: 100%; justify-content: center; gap: 8px; font-weight: 600; padding: 12px;">
                    <i class="ri-map-pin-line"></i> Cari Koordinat Lat/Long Otomatis
                </button>
                <small id="lookup-status" style="display: block; margin-top: 8px; font-size: 13px; font-weight: 500; color: var(--text-secondary);"></small>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="latitude">Latitude (Garis Lintang)</label>
                    <input type="text" id="latitude" name="latitude" class="form-control" placeholder="Contoh: -6.1894" value="{{ old('latitude') }}" required>
                </div>

                <div class="form-group">
                    <label for="longitude">Longitude (Garis Bujur)</label>
                    <input type="text" id="longitude" name="longitude" class="form-control" placeholder="Contoh: 106.7628" value="{{ old('longitude') }}" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 14px; margin-top: 10px;">
                <i class="ri-save-line"></i> Simpan Wilayah
            </button>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnLookup = document.getElementById('btn-lookup-coordinates');
        const statusEl = document.getElementById('lookup-status');
        const inputName = document.getElementById('name');
        const inputKecamatan = document.getElementById('kecamatan');
        const inputKabupaten = document.getElementById('kabupaten');
        const inputLatitude = document.getElementById('latitude');
        const inputLongitude = document.getElementById('longitude');

        if (btnLookup) {
            btnLookup.addEventListener('click', function() {
                const name = inputName.value.trim();
                const kecamatan = inputKecamatan.value.trim();
                const kabupaten = inputKabupaten.value.trim();

                if (!name || !kecamatan || !kabupaten) {
                    statusEl.style.color = 'var(--danger)';
                    statusEl.textContent = 'Harap isi Nama Desa, Kecamatan, dan Kabupaten terlebih dahulu.';
                    return;
                }

                statusEl.style.color = 'var(--text-secondary)';
                statusEl.textContent = 'Mencari koordinat...';
                btnLookup.disabled = true;

                // Query format: name, kecamatan, kabupaten, Indonesia
                const query = `${name}, ${kecamatan}, ${kabupaten}, Indonesia`;
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`;

                fetch(url, {
                    headers: {
                        'User-Agent': 'HealthCareApp/1.0'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat).toFixed(6);
                        const lon = parseFloat(data[0].lon).toFixed(6);
                        
                        inputLatitude.value = lat;
                        inputLongitude.value = lon;
                        
                        statusEl.style.color = 'var(--success)';
                        statusEl.textContent = `Berhasil menemukan koordinat! (Lat: ${lat}, Long: ${lon})`;
                    } else {
                        // Attempt fallback lookup without village name
                        statusEl.textContent = 'Desa tidak ditemukan secara spesifik, mencari koordinat Kecamatan...';
                        const fallbackQuery = `${kecamatan}, ${kabupaten}, Indonesia`;
                        const fallbackUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fallbackQuery)}&limit=1`;
                        
                        return fetch(fallbackUrl, {
                            headers: {
                                'User-Agent': 'HealthCareApp/1.0'
                            }
                        })
                        .then(resp => resp.json())
                        .then(fallbackData => {
                            if (fallbackData && fallbackData.length > 0) {
                                const lat = parseFloat(fallbackData[0].lat).toFixed(6);
                                const lon = parseFloat(fallbackData[0].lon).toFixed(6);
                                
                                inputLatitude.value = lat;
                                inputLongitude.value = lon;
                                
                                statusEl.style.color = 'var(--warning)';
                                statusEl.textContent = `Menggunakan koordinat Kecamatan. (Lat: ${lat}, Long: ${lon})`;
                            } else {
                                statusEl.style.color = 'var(--danger)';
                                statusEl.textContent = 'Koordinat tidak ditemukan. Harap masukkan secara manual.';
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching geocoding:', error);
                    statusEl.style.color = 'var(--danger)';
                    statusEl.textContent = 'Terjadi kesalahan saat menghubungi server geocoding. Harap isi manual.';
                })
                .finally(() => {
                    btnLookup.disabled = false;
                });
            });
        }
    });
</script>
@endsection
