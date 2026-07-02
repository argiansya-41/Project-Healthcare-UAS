<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestockRequest;
use App\Models\Medicine;
use App\Models\MedicineTransaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class RestockApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = RestockRequest::with(['user', 'medicine']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(10);

        return view('admin.restock.index', compact('requests'));
    }

    public function process(Request $request, $id, $action)
    {
        $restock = RestockRequest::findOrFail($id);

        if ($restock->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        if (!in_array($action, ['approve', 'reject'])) {
            return back()->with('error', 'Aksi tidak valid.');
        }

        if ($action === 'approve') {
            $restock->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Add stock automatically to medicine
            $medicine = $restock->medicine;
            $medicine->increment('stock', $restock->quantity);

            // Record transaction
            MedicineTransaction::create([
                'medicine_id' => $medicine->id,
                'supplier_id' => null,
                'type' => 'in',
                'quantity' => $restock->quantity,
                'notes' => "Restock disetujui Admin. No Pengajuan: #{$restock->id}",
                'transaction_date' => now()->toDateString(),
                'user_id' => auth()->id(),
            ]);

            ActivityLog::log('approve_restock', "Approved restock request #{$restock->id} for {$medicine->name} (Qty: {$restock->quantity})", auth()->id());
            return redirect()->route('admin.restock.index')->with('success', 'Permintaan restock disetujui, stok obat otomatis ditambahkan.');
        } else {
            $restock->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            ActivityLog::log('reject_restock', "Rejected restock request #{$restock->id} for {$restock->medicine->name}", auth()->id());
            return redirect()->route('admin.restock.index')->with('success', 'Permintaan restock ditolak.');
        }
    }
}
