<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DiseaseReport;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    public function index(Request $request)
    {
        $query = DiseaseReport::with(['diseaseType', 'reporter'])
            ->where('status', 'verified');

        if ($request->filter === 'pending_treatment') {
            $query->whereNull('treatment_recommendation');
        } elseif ($request->filter === 'treated') {
            $query->whereNotNull('treatment_recommendation');
        }

        $reports = $query->latest()->paginate(10, ['*'], 'reports_page');

        // Fetch immunization records with complaints
        $complaintsQuery = \App\Models\ImmunizationRecord::with(['child.parent', 'vaccine', 'officer'])
            ->whereNotNull('vaccine_complaint');

        if ($request->filter === 'pending_treatment') {
            $complaintsQuery->whereNull('doctor_response');
        } elseif ($request->filter === 'treated') {
            $complaintsQuery->whereNotNull('doctor_response');
        }

        $complaints = $complaintsQuery->latest()->paginate(10, ['*'], 'complaints_page');

        return view('dokter.consultations.index', compact('reports', 'complaints'));
    }

    public function recommend(Request $request, $id)
    {
        $request->validate([
            'treatment_recommendation' => ['required', 'string'],
        ]);

        $report = DiseaseReport::findOrFail($id);

        if ($report->status !== 'verified') {
            return back()->with('error', 'Rekomendasi penanganan hanya dapat diberikan pada laporan yang sudah terverifikasi.');
        }

        $report->update([
            'treatment_recommendation' => $request->treatment_recommendation,
        ]);

        ActivityLog::log('add_treatment_recommendation', "Added medical treatment recommendation for patient {$report->patient_name} (Report #{$report->id})", auth()->id());

        return redirect()->route('kesehatan.reports.index')->with('success', 'Rekomendasi penanganan medis berhasil disimpan.');
    }

    public function respondToComplaint(Request $request, $id)
    {
        $request->validate([
            'doctor_response' => ['required', 'string'],
        ]);

        $record = \App\Models\ImmunizationRecord::with('child')->findOrFail($id);

        $record->update([
            'doctor_response' => $request->doctor_response,
        ]);

        ActivityLog::log('respond_vaccine_complaint', "Doctor responded to vaccine complaint for child {$record->child->name} (Record #{$record->id})", auth()->id());

        return redirect()->route('dokter.consultations.index')->with('success', 'Tanggapan medis keluhan vaksin berhasil disimpan.');
    }
}
