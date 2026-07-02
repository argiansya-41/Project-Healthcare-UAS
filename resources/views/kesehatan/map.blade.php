@extends('layouts.app')

@section('header-title', 'Peta Sebaran Kasus Penyakit')

@section('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map {
            width: 100%;
            height: 550px;
            border-radius: 20px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
            z-index: 1;
        }
        .map-popup h4 {
            margin-bottom: 4px;
            font-size: 14px;
            color: var(--danger);
        }
        .map-popup p {
            margin: 0;
            font-size: 12px;
            color: var(--text-secondary);
        }
    </style>
@endsection

@section('content')
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <div>
                <h3 style="font-size: 18px; font-weight: 700;">Sistem Informasi Geografis Penyakit</h3>
                <p style="color: var(--text-secondary); font-size: 13px; margin-top: 4px;">Peta interaktif sebaran kasus penyakit menular terverifikasi di wilayah Puskesmas.</p>
            </div>
            <a href="{{ route('kesehatan.reports.index') }}" class="btn btn-secondary"><i class="ri-list-check"></i> Daftar Laporan</a>
        </div>

        <!-- Map element -->
        <div id="map"></div>
    </div>
@endsection

@section('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Center map around Jakarta (coordinates matching seeded reports: -6.1894, 106.7628)
            const map = L.map('map').setView([-6.1894, 106.7628], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Seeded cases injection
            const cases = [
                @foreach($cases as $case)
                {
                    id: {{ $case->id }},
                    lat: {{ $case->latitude }},
                    lng: {{ $case->longitude }},
                    name: "{{ $case->patient_name }}",
                    age: {{ $case->patient_age }},
                    disease: "{{ $case->diseaseType->name }}",
                    code: "{{ $case->diseaseType->code }}",
                    severity: "{{ $case->severity }}",
                    date: "{{ $case->report_date->format('d/m/Y') }}"
                },
                @endforeach
            ];

            // Add markers
            cases.forEach(function (c) {
                // Color marker based on severity
                let markerColor = 'blue';
                if (c.severity === 'berat') markerColor = 'red';
                else if (c.severity === 'sedang') markerColor = 'orange';

                const marker = L.marker([c.lat, c.lng]).addTo(map);
                
                const popupContent = `
                    <div class="map-popup">
                        <h4>${c.disease} (${c.code})</h4>
                        <p><strong>Pasien:</strong> ${c.name} (${c.age} Tahun)</p>
                        <p><strong>Tingkat:</strong> ${c.severity.toUpperCase()}</p>
                        <p><strong>Tanggal Lapor:</strong> ${c.date}</p>
                        <p style="margin-top: 8px;"><a href="/kesehatan/reports/${c.id}" target="_blank" style="color: var(--accent-color); font-weight:600; text-decoration:none;">Lihat Detail &rarr;</a></p>
                    </div>
                `;
                marker.bindPopup(popupContent);
            });
        });
    </script>
@endsection
