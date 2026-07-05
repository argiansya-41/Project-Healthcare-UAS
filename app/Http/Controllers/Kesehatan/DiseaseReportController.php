<?php

namespace App\Http\Controllers\Kesehatan;

use App\Http\Controllers\Controller;
use App\Models\DiseaseReport;
use App\Models\DiseaseType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DiseaseReportController extends Controller
{
    public function index(Request $request)
    {
        $query = DiseaseReport::with(['diseaseType', 'reporter', 'village']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                  ->orWhere('patient_nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('disease_type_id')) {
            $query->where('disease_type_id', $request->disease_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $reports = $query->latest()->paginate(10);
        $diseaseTypes = DiseaseType::all();

        return view('kesehatan.reports.index', compact('reports', 'diseaseTypes'));
    }

    public function create()
    {
        $diseaseTypes = DiseaseType::all();
        $villages = \App\Models\Village::orderBy('name', 'asc')->get();
        return view('kesehatan.reports.create', compact('diseaseTypes', 'villages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_nik' => ['required', 'string', 'size:16'],
            'patient_age' => ['required', 'integer', 'min:0'],
            'patient_gender' => ['required', 'in:L,P'],
            'patient_address' => ['required', 'string'],
            'disease_type_id' => ['required', 'exists:disease_types,id'],
            'symptoms' => ['required', 'string'],
            'severity' => ['required', 'in:ringan,sedang,berat'],
            'report_date' => ['required', 'date'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'village_id' => ['required', 'exists:villages,id'],
        ]);

        $data = $request->all();
        $data['reporter_id'] = auth()->id();
        
        // If a health officer reports, it can be auto-verified, or pending.
        // Let's default health officers to verified, and operators/warga to pending.
        if (auth()->user()->role === 'petugas_medis') {
            $data['status'] = 'verified';
            $data['verified_by'] = auth()->id();
            $data['verification_notes'] = 'Laporan diinput langsung oleh Petugas Kesehatan.';
        } else {
            $data['status'] = 'pending';
        }

        $report = DiseaseReport::create($data);

        ActivityLog::log('create_disease_report', "Reported disease case: {$report->patient_name} - {$report->diseaseType->name}", auth()->id());

        return redirect()->route('kesehatan.reports.index')->with('success', 'Laporan penyakit berhasil dibuat.');
    }

    public function show($id)
    {
        $report = DiseaseReport::with(['diseaseType', 'reporter', 'verifiedBy'])->findOrFail($id);
        return view('kesehatan.reports.show', compact('report'));
    }

    public function destroy(DiseaseReport $report)
    {
        $name = $report->patient_name;
        $report->delete();

        ActivityLog::log('delete_disease_report', "Deleted disease report for {$name}", auth()->id());

        return redirect()->route('kesehatan.reports.index')->with('success', 'Laporan penyakit berhasil dihapus.');
    }

    public function map()
    {
        // Get verified cases with coords
        $cases = DiseaseReport::with('diseaseType')
            ->where('status', 'verified')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return view('kesehatan.map', compact('cases'));
    }

    public function export(Request $request)
    {
        $query = DiseaseReport::with(['diseaseType', 'reporter', 'village']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                  ->orWhere('patient_nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('disease_type_id')) {
            $query->where('disease_type_id', $request->disease_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $reports = $query->latest()->get();

        $filename = 'laporan_kasus_penyakit_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function() use ($reports) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Tanggal Lapor',
                'Nama Pasien',
                'NIK',
                'Umur',
                'Jenis Kelamin (L/P)',
                'Alamat',
                'Desa/Kelurahan',
                'Penyakit',
                'Kode Penyakit',
                'Gejala',
                'Tingkat Keparahan (ringan/sedang/berat)',
                'Status Verifikasi (pending/verified/rejected)',
                'Rekomendasi Medis',
                'Latitude',
                'Longitude'
            ]);

            foreach ($reports as $rep) {
                fputcsv($file, [
                    $rep->report_date ? $rep->report_date->format('Y-m-d') : '',
                    $rep->patient_name,
                    $rep->patient_nik,
                    $rep->patient_age,
                    $rep->patient_gender,
                    $rep->patient_address,
                    $rep->village ? $rep->village->name : '',
                    $rep->diseaseType ? $rep->diseaseType->name : '',
                    $rep->diseaseType ? $rep->diseaseType->code : '',
                    $rep->symptoms,
                    $rep->severity,
                    $rep->status,
                    $rep->treatment_recommendation ?? '',
                    $rep->latitude,
                    $rep->longitude
                ]);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ]);
    }

    public function template()
    {
        $filename = 'template_laporan_kasus.csv';

        return response()->streamDownload(function() {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Nama Pasien',
                'NIK',
                'Umur',
                'Jenis Kelamin (L/P)',
                'Alamat',
                'Desa/Kelurahan',
                'Kode Penyakit',
                'Gejala',
                'Tingkat Keparahan (ringan/sedang/berat)',
                'Tanggal Lapor (YYYY-MM-DD)',
                'Latitude',
                'Longitude'
            ]);

            fputcsv($file, [
                'Budi Santoso',
                '3201010101010099',
                '30',
                'L',
                'Meruya Utara',
                'Meruya Utara',
                'DBD',
                'Demam tinggi naik turun, bintik merah',
                'sedang',
                date('Y-m-d'),
                '-6.20876000',
                '106.74567000'
            ]);

            fputcsv($file, [
                'Siti Aminah',
                '3201010101010088',
                '25',
                'P',
                'Kedoya Selatan',
                'Kedoya Selatan',
                'TBC',
                'Batuk berdahak lebih dari 2 minggu, demam ringan',
                'ringan',
                date('Y-m-d'),
                '-6.18954000',
                '106.75843000'
            ]);

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:2048']
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        if ($extension !== 'csv') {
            return back()->with('error', 'File harus berupa dokumen dengan format CSV.');
        }

        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Gagal membuka file.');
        }

        // Detect delimiter
        $firstLine = fgets($handle);
        $delimiter = ',';
        if (strpos($firstLine, ';') !== false && strpos($firstLine, ',') === false) {
            $delimiter = ';';
        }
        rewind($handle);

        $headers = fgetcsv($handle, 0, $delimiter);
        if (!$headers) {
            fclose($handle);
            return back()->with('error', 'Format file CSV tidak valid atau kosong.');
        }

        // Remove UTF-8 BOM if present
        $bom = pack('H*', 'EFBBBF');
        $headers[0] = preg_replace("/^$bom/", '', $headers[0]);
        $headers = array_map('trim', $headers);

        // Map column names
        $map = [];
        foreach ($headers as $idx => $header) {
            $cleaned = strtolower(trim($header));
            if (strpos($cleaned, 'nama pasien') !== false || $cleaned === 'nama') $map['patient_name'] = $idx;
            elseif (strpos($cleaned, 'nik') !== false) $map['patient_nik'] = $idx;
            elseif (strpos($cleaned, 'umur') !== false || strpos($cleaned, 'usia') !== false) $map['patient_age'] = $idx;
            elseif (strpos($cleaned, 'jenis kelamin') !== false || $cleaned === 'jk' || $cleaned === 'gender') $map['patient_gender'] = $idx;
            elseif (strpos($cleaned, 'alamat') !== false) $map['patient_address'] = $idx;
            elseif (strpos($cleaned, 'desa') !== false || strpos($cleaned, 'kelurahan') !== false || $cleaned === 'village') $map['village_id'] = $idx;
            elseif (strpos($cleaned, 'kode penyakit') !== false || $cleaned === 'disease_code' || $cleaned === 'penyakit') $map['disease_type_id'] = $idx;
            elseif (strpos($cleaned, 'gejala') !== false || $cleaned === 'symptoms') $map['symptoms'] = $idx;
            elseif (strpos($cleaned, 'keparahan') !== false || $cleaned === 'severity') $map['severity'] = $idx;
            elseif (strpos($cleaned, 'tanggal') !== false || strpos($cleaned, 'date') !== false) $map['report_date'] = $idx;
            elseif (strpos($cleaned, 'latitude') !== false || $cleaned === 'lat') $map['latitude'] = $idx;
            elseif (strpos($cleaned, 'longitude') !== false || $cleaned === 'long' || $cleaned === 'lng') $map['longitude'] = $idx;
        }

        $required = ['patient_name', 'patient_nik', 'patient_age', 'patient_gender', 'patient_address', 'village_id', 'disease_type_id', 'symptoms', 'severity', 'report_date'];
        $missing = [];
        foreach ($required as $req) {
            if (!isset($map[$req])) {
                $missing[] = $req;
            }
        }

        if (count($missing) > 0) {
            fclose($handle);
            $friendlyNames = [
                'patient_name' => 'Nama Pasien',
                'patient_nik' => 'NIK',
                'patient_age' => 'Umur',
                'patient_gender' => 'Jenis Kelamin',
                'patient_address' => 'Alamat',
                'village_id' => 'Desa/Kelurahan',
                'disease_type_id' => 'Kode Penyakit',
                'symptoms' => 'Gejala',
                'severity' => 'Tingkat Keparahan',
                'report_date' => 'Tanggal Lapor'
            ];
            $missingFriendly = array_map(function($key) use ($friendlyNames) {
                return $friendlyNames[$key] ?? $key;
            }, $missing);
            return back()->with('error', 'Kolom wajib berikut tidak ditemukan di file CSV: ' . implode(', ', $missingFriendly));
        }

        $rowNum = 1;
        $errors = [];
        $importedCount = 0;

        $villages = \App\Models\Village::all()->pluck('id', 'name');
        $villagesMap = [];
        foreach ($villages as $name => $id) {
            $villagesMap[strtolower(trim($name))] = $id;
        }

        $diseaseTypes = \App\Models\DiseaseType::all();
        $diseasesMap = [];
        foreach ($diseaseTypes as $dt) {
            $diseasesMap[strtolower(trim($dt->code))] = $dt->id;
            $diseasesMap[strtolower(trim($dt->name))] = $dt->id;
        }

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNum++;
                // Skip empty lines
                if (empty($row) || (count($row) === 1 && empty($row[0]))) {
                    continue;
                }

                $pName = trim($row[$map['patient_name']] ?? '');
                $pNik = trim($row[$map['patient_nik']] ?? '');
                $pAge = trim($row[$map['patient_age']] ?? '');
                $pGender = strtoupper(trim($row[$map['patient_gender']] ?? ''));
                $pAddress = trim($row[$map['patient_address']] ?? '');
                $villageName = trim($row[$map['village_id']] ?? '');
                $diseaseCodeOrName = trim($row[$map['disease_type_id']] ?? '');
                $symptoms = trim($row[$map['symptoms']] ?? '');
                $severity = strtolower(trim($row[$map['severity']] ?? ''));
                $reportDateRaw = trim($row[$map['report_date']] ?? '');
                $lat = isset($map['latitude']) ? trim($row[$map['latitude']] ?? '') : null;
                $lng = isset($map['longitude']) ? trim($row[$map['longitude']] ?? '') : null;

                $rowErrors = [];

                if (empty($pName)) {
                    $rowErrors[] = 'Nama Pasien tidak boleh kosong.';
                }

                if (empty($pNik) || strlen($pNik) !== 16) {
                    $rowErrors[] = 'NIK harus tepat 16 karakter.';
                }

                if (empty($pAge) || !is_numeric($pAge) || intval($pAge) < 0) {
                    $rowErrors[] = 'Umur harus berupa angka positif.';
                }

                if ($pGender !== 'L' && $pGender !== 'P') {
                    $rowErrors[] = 'Jenis Kelamin harus L atau P.';
                }

                if (empty($pAddress)) {
                    $rowErrors[] = 'Alamat tidak boleh kosong.';
                }

                $villageId = null;
                $cleanVillageName = strtolower(trim($villageName));
                if (empty($villageName)) {
                    $rowErrors[] = 'Desa/Kelurahan tidak boleh kosong.';
                } elseif (!isset($villagesMap[$cleanVillageName])) {
                    $rowErrors[] = "Desa/Kelurahan '{$villageName}' tidak terdaftar.";
                } else {
                    $villageId = $villagesMap[$cleanVillageName];
                }

                $diseaseTypeId = null;
                $cleanDisease = strtolower(trim($diseaseCodeOrName));
                if (empty($diseaseCodeOrName)) {
                    $rowErrors[] = 'Kode/Nama Penyakit tidak boleh kosong.';
                } elseif (!isset($diseasesMap[$cleanDisease])) {
                    $rowErrors[] = "Penyakit '{$diseaseCodeOrName}' tidak terdaftar.";
                } else {
                    $diseaseTypeId = $diseasesMap[$cleanDisease];
                }

                if (empty($symptoms)) {
                    $rowErrors[] = 'Gejala tidak boleh kosong.';
                }

                if ($severity !== 'ringan' && $severity !== 'sedang' && $severity !== 'berat') {
                    $rowErrors[] = 'Tingkat keparahan harus ringan, sedang, atau berat.';
                }

                $reportDate = null;
                if (empty($reportDateRaw)) {
                    $rowErrors[] = 'Tanggal Lapor tidak boleh kosong.';
                } else {
                    try {
                        $reportDate = \Carbon\Carbon::parse($reportDateRaw);
                    } catch (\Exception $e) {
                        $rowErrors[] = "Tanggal Lapor '{$reportDateRaw}' tidak valid.";
                    }
                }

                if (!empty($lat) && !is_numeric($lat)) {
                    $rowErrors[] = 'Latitude harus berupa angka.';
                }
                if (!empty($lng) && !is_numeric($lng)) {
                    $rowErrors[] = 'Longitude harus berupa angka.';
                }

                if (count($rowErrors) > 0) {
                    $errors[] = "Baris {$rowNum}: " . implode(' ', $rowErrors);
                } else {
                    $data = [
                        'patient_name' => $pName,
                        'patient_nik' => $pNik,
                        'patient_age' => intval($pAge),
                        'patient_gender' => $pGender,
                        'patient_address' => $pAddress,
                        'village_id' => $villageId,
                        'disease_type_id' => $diseaseTypeId,
                        'symptoms' => $symptoms,
                        'severity' => $severity,
                        'report_date' => $reportDate->format('Y-m-d'),
                        'latitude' => !empty($lat) ? floatval($lat) : null,
                        'longitude' => !empty($lng) ? floatval($lng) : null,
                        'reporter_id' => auth()->id(),
                        'status' => 'pending',
                        'verified_by' => null,
                        'verification_notes' => null,
                    ];

                    DiseaseReport::create($data);
                    $importedCount++;
                }
            }

            fclose($handle);

            if (count($errors) > 0) {
                \Illuminate\Support\Facades\DB::rollBack();
                return back()->with('import_errors', $errors)->with('error', 'Gagal mengimpor file CSV karena terdapat kesalahan data.');
            }

            \Illuminate\Support\Facades\DB::commit();

            if ($importedCount > 0) {
                ActivityLog::log('import_disease_reports', "Diimpor {$importedCount} laporan kasus penyakit dari file CSV", auth()->id());
                return back()->with('success', "Berhasil mengimpor {$importedCount} laporan kasus penyakit.");
            } else {
                return back()->with('error', 'Tidak ada data laporan yang diimpor.');
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses file: ' . $e->getMessage());
        }
    }
}
