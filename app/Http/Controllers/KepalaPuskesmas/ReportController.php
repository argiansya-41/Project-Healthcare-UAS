<?php

namespace App\Http\Controllers\KepalaPuskesmas;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\DiseaseReport;
use App\Models\Child;
use App\Models\ImmunizationRecord;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('kepala.reports.index');
    }

    public function export($module, $format)
    {
        if (!in_array($module, ['obat', 'penyakit', 'imunisasi'])) {
            abort(404, 'Modul laporan tidak ditemukan.');
        }

        // We support printable browser print preview
        if ($format === 'print') {
            switch ($module) {
                case 'obat':
                    $data = Medicine::with(['category', 'unit'])->orderBy('name', 'asc')->get();
                    $title = 'Laporan Stok Obat Terintegrasi';
                    return view('kepala.exports.obat', compact('data', 'title'));
                case 'penyakit':
                    $data = DiseaseReport::with(['diseaseType', 'reporter'])->orderBy('report_date', 'desc')->get();
                    $title = 'Laporan Sebaran Kasus Penyakit';
                    return view('kepala.exports.penyakit', compact('data', 'title'));
                case 'imunisasi':
                    $data = ImmunizationRecord::with(['child.parent', 'vaccine'])->orderBy('scheduled_date', 'asc')->get();
                    $title = 'Laporan Riwayat Pelaksanaan Imunisasi';
                    return view('kepala.exports.imunisasi', compact('data', 'title'));
            }
        }

        // Fallback for other formats (like excel placeholder)
        return back()->with('error', "Format ekspor {$format} belum dikonfigurasi.");
    }
}
