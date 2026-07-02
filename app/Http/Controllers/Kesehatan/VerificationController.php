<?php

namespace App\Http\Controllers\Kesehatan;

use App\Http\Controllers\Controller;
use App\Models\DiseaseReport;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index()
    {
        $reports = DiseaseReport::with(['diseaseType', 'reporter'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('kesehatan.verification.index', compact('reports'));
    }

    public function process(Request $request, $id, $action)
    {
        $report = DiseaseReport::findOrFail($id);

        if ($report->status !== 'pending') {
            return back()->with('error', 'Laporan ini sudah diverifikasi sebelumnya.');
        }

        if (!in_array($action, ['verify', 'reject'])) {
            return back()->with('error', 'Aksi tidak valid.');
        }

        $request->validate([
            'verification_notes' => ['required', 'string'],
        ]);

        if ($action === 'verify') {
            $report->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verification_notes' => $request->verification_notes,
            ]);

            ActivityLog::log('verify_disease_report', "Verified disease report #{$report->id} for patient {$report->patient_name}", auth()->id());
            return redirect()->route('kesehatan.verification.index')->with('success', 'Laporan kasus penyakit berhasil diverifikasi.');
        } else {
            $report->update([
                'status' => 'rejected',
                'verified_by' => auth()->id(),
                'verification_notes' => $request->verification_notes,
            ]);

            ActivityLog::log('reject_disease_report', "Rejected disease report #{$report->id} for patient {$report->patient_name}", auth()->id());
            return redirect()->route('kesehatan.verification.index')->with('success', 'Laporan kasus penyakit ditolak.');
        }
    }
}
