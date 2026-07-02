<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use Illuminate\Http\Request;

class WargaMedicineController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::with(['category', 'unit']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $medicines = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();
        $categories = MedicineCategory::orderBy('name', 'asc')->get();

        return view('warga.medicines.index', compact('medicines', 'categories'));
    }
}
