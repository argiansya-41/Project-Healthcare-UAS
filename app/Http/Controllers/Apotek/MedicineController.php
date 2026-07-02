<?php

namespace App\Http\Controllers\Apotek;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicineUnit;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::with(['category', 'unit']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filter === 'low_stock') {
            $query->whereColumn('stock', '<=', 'min_stock');
        } elseif ($request->filter === 'expired') {
            $query->where('expiration_date', '<', now()->toDateString());
        }

        $medicines = $query->latest()->paginate(10);
        $categories = MedicineCategory::all();

        return view('apotek.medicines.index', compact('medicines', 'categories'));
    }

    public function create()
    {
        $categories = MedicineCategory::all();
        $units = MedicineUnit::all();
        return view('apotek.medicines.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'unique:medicines'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:medicine_categories,id'],
            'unit_id' => ['required', 'exists:medicine_units,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'expiration_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $medicine = Medicine::create($request->all());

        ActivityLog::log('create_medicine', "Added new medicine: {$medicine->name} (#{$medicine->code})", auth()->id());

        return redirect()->route('apotek.medicines.index')->with('success', 'Obat berhasil ditambahkan.');
    }

    public function edit(Medicine $medicine)
    {
        $categories = MedicineCategory::all();
        $units = MedicineUnit::all();
        return view('apotek.medicines.edit', compact('medicine', 'categories', 'units'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $request->validate([
            'code' => ['required', 'string', 'unique:medicines,code,'.$medicine->id],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:medicine_categories,id'],
            'unit_id' => ['required', 'exists:medicine_units,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'expiration_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $medicine->update($request->all());

        ActivityLog::log('update_medicine', "Updated medicine: {$medicine->name} (#{$medicine->code})", auth()->id());

        return redirect()->route('apotek.medicines.index')->with('success', 'Obat berhasil diperbarui.');
    }

    public function destroy(Medicine $medicine)
    {
        $name = $medicine->name;
        $code = $medicine->code;
        $medicine->delete();

        ActivityLog::log('delete_medicine', "Deleted medicine: {$name} (#{$code})", auth()->id());

        return redirect()->route('apotek.medicines.index')->with('success', 'Obat berhasil dihapus.');
    }

    public function reports()
    {
        $medicines = Medicine::with(['category', 'unit'])->orderBy('stock', 'asc')->get();
        return view('apotek.reports', compact('medicines'));
    }
}
