<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BpsController extends Controller
{
    public function index()
    {
        $url = "https://webapi.bps.go.id/v1/api/interoperabilitas/datasource/simdasi/id/25/tahun/2025/id_tabel/a05CZmFhT0JWY0lBd2g0cW80S0xiZz09/wilayah/0000000/key/262a417d337792902a3c2ed5650553fd";

        // Cache the parsed BPS data for 12 hours (43200 seconds)
        $bpsData = Cache::remember('bps_statistics_data', 43200, function () use ($url) {
            $arrContextOptions = [
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
                "http" => [
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
                ]
            ];

            $response = @file_get_contents($url, false, stream_context_create($arrContextOptions));

            if ($response === FALSE) {
                return null;
            }

            $decoded = json_decode($response, true);
            if (isset($decoded['data'][1])) {
                return $decoded['data'][1];
            }

            return null;
        });

        if (!$bpsData) {
            return view('bps.index', [
                'error' => 'Gagal mengambil data dari API BPS. Silakan periksa koneksi internet Anda atau coba lagi beberapa saat lagi.',
                'judul_tabel' => 'Kasus Penyakit Menurut Provinsi dan Jenis Penyakit, 2025',
                'kolom' => [],
                'data' => [],
                'catatan' => 'Data tidak tersedia',
                'sumber' => 'Kementerian Kesehatan / BPS',
                'table_updated' => '-'
            ]);
        }

        return view('bps.index', [
            'error' => null,
            'judul_tabel' => $bpsData['judul_tabel'] ?? 'Kasus Penyakit Menurut Provinsi dan Jenis Penyakit, 2025',
            'kolom' => $bpsData['kolom'] ?? [],
            'data' => $bpsData['data'] ?? [],
            'catatan' => $bpsData['catatan'] ?? '',
            'sumber' => $bpsData['sumber'] ?? '',
            'table_updated' => $bpsData['table_updated'] ?? ''
        ]);
    }
}
