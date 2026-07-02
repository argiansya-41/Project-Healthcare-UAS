<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\RestockRequest;
use App\Models\DiseaseReport;
use App\Models\DiseaseType;
use App\Models\Child;
use App\Models\ImmunizationRecord;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return $this->adminDashboard($user);
            case 'apoteker':
                return $this->apotekerDashboard($user);
            case 'petugas_medis':
                return $this->petugasMedisDashboard($user);
            case 'warga':
                return $this->wargaDashboard($user);
            default:
                abort(403, 'Role tidak dikenali.');
        }
    }

    private function adminDashboard($user)
    {
        $stats = [
            'total_users' => User::count(),
            'total_medicines' => Medicine::count(),
            'low_stock_count' => Medicine::whereColumn('stock', '<=', 'min_stock')->count(),
            'total_disease_reports' => DiseaseReport::count(),
            'total_children' => Child::count(),
            'pending_restocks' => RestockRequest::where('status', 'pending')->count(),
            'recent_logs' => ActivityLog::with('user')->latest()->limit(5)->get(),
            'immunization_success_rate' => ImmunizationRecord::count() > 0 
                ? round((ImmunizationRecord::where('status', 'completed')->count() / ImmunizationRecord::count()) * 100, 1) 
                : 100,
        ];

        return view('dashboard.admin', compact('stats'));
    }

    private function apotekerDashboard($user)
    {
        $stats = [
            'total_medicines' => Medicine::count(),
            'total_categories' => MedicineCategory::count(),
            'low_stock_count' => Medicine::whereColumn('stock', '<=', 'min_stock')->count(),
            'expired_count' => Medicine::where('expiration_date', '<', now()->toDateString())->count(),
            'pending_restock_requests' => RestockRequest::where('status', 'pending')->count(),
            'recent_medicines' => Medicine::with('category', 'unit')->latest()->limit(5)->get(),
        ];

        return view('dashboard.apoteker', compact('stats'));
    }

    private function petugasMedisDashboard($user)
    {
        $month = request('severity_month');
        $severityQuery = DiseaseReport::query();
        if ($month) {
            $severityQuery->whereMonth('report_date', $month)
                          ->whereYear('report_date', now()->year);
        }

        $stats = [
            // Pelaporan Penyakit
            'total_reports' => DiseaseReport::count(),
            'pending_reports' => DiseaseReport::where('status', 'pending')->count(),
            'verified_reports' => DiseaseReport::where('status', 'verified')->count(),
            'recent_reports' => DiseaseReport::with('diseaseType')->latest()->limit(5)->get(),
            'severity_stats' => [
                'ringan' => (clone $severityQuery)->where('severity', 'ringan')->count(),
                'sedang' => (clone $severityQuery)->where('severity', 'sedang')->count(),
                'berat' => (clone $severityQuery)->where('severity', 'berat')->count(),
            ],

            // Konsultasi Medis / Rekomendasi
            'reports_need_treatment' => DiseaseReport::where('status', 'verified')
                ->whereNull('treatment_recommendation')
                ->count(),
            'recent_disease_cases' => DiseaseReport::with('diseaseType')
                ->where('status', 'verified')
                ->latest()
                ->limit(5)
                ->get(),

            // Imunisasi
            'total_children' => Child::count(),
            'scheduled_count' => ImmunizationRecord::where('status', 'scheduled')->count(),
            'completed_count' => ImmunizationRecord::where('status', 'completed')->count(),
            'missed_count' => ImmunizationRecord::where('status', 'missed')->count(),
            'upcoming_schedules' => ImmunizationRecord::with('child', 'vaccine')
                ->where('status', 'scheduled')
                ->where('scheduled_date', '>=', now()->toDateString())
                ->orderBy('scheduled_date', 'asc')
                ->limit(5)
                ->get(),
        ];

        return view('dashboard.petugas_medis', compact('stats'));
    }

    private function wargaDashboard($user)
    {
        // Get children owned by this user
        $children = Child::where('parent_id', $user->id)->get();
        $childIds = $children->pluck('id');

        $stats = [
            'total_children' => $children->count(),
            'upcoming_vaccinations' => ImmunizationRecord::with('child', 'vaccine')
                ->whereIn('child_id', $childIds)
                ->where('status', 'scheduled')
                ->orderBy('scheduled_date', 'asc')
                ->get(),
            'my_children' => $children,
        ];

        return view('dashboard.warga', compact('stats'));
    }
}
