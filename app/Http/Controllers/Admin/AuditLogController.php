<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->query('action'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->latest()->paginate(15)->withQueryString();

        return view('admin.logs', compact('logs'));
    }

    public function destroy($id)
    {
        $log = ActivityLog::findOrFail($id);
        $description = $log->description;
        $log->delete();

        // Record the deletion event for the audit trail
        ActivityLog::log('delete_log', "Menghapus log aktivitas: {$description}", auth()->id());

        return redirect()->back()->with('success', 'Log aktivitas berhasil dihapus.');
    }

    public function clearAll()
    {
        ActivityLog::query()->delete();

        // Record the clear log event in the audit trail
        ActivityLog::log('clear_logs', 'Membersihkan semua log aktivitas sistem', auth()->id());

        return redirect()->back()->with('success', 'Semua log aktivitas berhasil dibersihkan.');
    }
}
