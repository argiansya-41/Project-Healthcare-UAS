<?php

namespace App\Http\Controllers\Apotek;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\RestockRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class RestockRequestController extends Controller
{
    public function index()
    {
        $requests = RestockRequest::with(['medicine', 'approvedBy'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('apotek.restock.index', compact('requests'));
    }

    public function create()
    {
        $medicines = Medicine::orderBy('name', 'asc')->get();
        return view('apotek.restock.create', compact('medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_id' => ['required', 'exists:medicines,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $req = RestockRequest::create([
            'user_id' => auth()->id(),
            'medicine_id' => $request->medicine_id,
            'quantity' => $request->quantity,
            'status' => 'pending',
        ]);

        $medicineName = Medicine::find($request->medicine_id)->name;

        ActivityLog::log('request_restock', "Requested restock #{$req->id} for {$medicineName} (Qty: {$request->quantity})", auth()->id());

        return redirect()->route('apotek.restock-requests.index')->with('success', 'Permintaan restock berhasil diajukan.');
    }
}
