<?php

namespace App\Http\Controllers\Apotek;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Supplier;
use App\Models\MedicineTransaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicineTransaction::with(['medicine', 'supplier', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('medicine', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest()->paginate(10);

        return view('apotek.transactions.index', compact('transactions'));
    }

    public function create()
    {
        $medicines = Medicine::orderBy('name', 'asc')->get();
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        return view('apotek.transactions.create', compact('medicines', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_id' => ['required', 'exists:medicines,id'],
            'type' => ['required', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'transaction_date' => ['required', 'date'],
            'supplier_id' => ['required_if:type,in', 'nullable', 'exists:suppliers,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $medicine = Medicine::findOrFail($request->medicine_id);

        if ($request->type === 'out' && $medicine->stock < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok obat tidak mencukupi untuk transaksi keluar ini.'])->withInput();
        }

        // Create transaction
        $transaction = MedicineTransaction::create([
            'medicine_id' => $request->medicine_id,
            'supplier_id' => $request->type === 'in' ? $request->supplier_id : null,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'notes' => $request->notes,
            'transaction_date' => $request->transaction_date,
            'user_id' => auth()->id(),
        ]);

        // Adjust stock
        if ($request->type === 'in') {
            $medicine->increment('stock', $request->quantity);
        } else {
            $medicine->decrement('stock', $request->quantity);
        }

        ActivityLog::log('medicine_transaction', "Recorded transaction ({$request->type}) for {$medicine->name} (Qty: {$request->quantity})", auth()->id());

        return redirect()->route('apotek.transactions.index')->with('success', 'Transaksi stok berhasil disimpan.');
    }

    public function destroy($id)
    {
        $transaction = MedicineTransaction::findOrFail($id);
        $medicine = Medicine::findOrFail($transaction->medicine_id);

        if ($transaction->type === 'in') {
            if ($medicine->stock < $transaction->quantity) {
                return back()->with('error', "Stok obat {$medicine->name} tidak mencukupi untuk membatalkan/menghapus transaksi masuk ini (stok saat ini: {$medicine->stock}, kuantiti transaksi: {$transaction->quantity}).");
            }
            $medicine->decrement('stock', $transaction->quantity);
        } else {
            $medicine->increment('stock', $transaction->quantity);
        }

        $transaction->delete();

        ActivityLog::log('medicine_transaction', "Deleted transaction ID {$transaction->id} ({$transaction->type}) for {$medicine->name} (Qty: {$transaction->quantity})", auth()->id());

        return redirect()->route('apotek.transactions.index')->with('success', 'Transaksi berhasil dihapus dan stok obat disesuaikan.');
    }

    public function clearAll()
    {
        MedicineTransaction::query()->delete();

        ActivityLog::log('medicine_transaction', 'Membersihkan semua riwayat transaksi stok obat', auth()->id());

        return redirect()->route('apotek.transactions.index')->with('success', 'Semua riwayat transaksi stok obat berhasil dibersihkan.');
    }
}
