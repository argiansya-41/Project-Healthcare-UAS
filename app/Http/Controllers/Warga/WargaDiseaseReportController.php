<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\DiseaseReport;
use App\Models\DiseaseType;
use App\Models\Village;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class WargaDiseaseReportController extends Controller
{
    public function index()
    {
        $reports = DiseaseReport::with(['diseaseType', 'village'])
            ->where('reporter_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('warga.reports.index', compact('reports'));
    }

    public function create()
    {
        $diseaseTypes = DiseaseType::all();
        $villages = Village::orderBy('name', 'asc')->get();

        return view('warga.reports.create', compact('diseaseTypes', 'villages'));
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
        $data['status'] = 'pending'; // Citizens always submit reports with pending status

        $report = DiseaseReport::create($data);

        ActivityLog::log('create_disease_report_warga', "Warga reported disease case: {$report->patient_name} - {$report->diseaseType->name}", auth()->id());

        return redirect()->route('warga.reports.index')->with('success', 'Laporan penyakit berhasil dikirim dan menunggu verifikasi.');
    }

    public function show($id)
    {
        $report = DiseaseReport::with(['diseaseType', 'reporter', 'verifiedBy', 'village'])
            ->where('reporter_id', auth()->id())
            ->findOrFail($id);

        return view('warga.reports.show', compact('report'));
    }
}
