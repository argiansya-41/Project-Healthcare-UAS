<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImmunizationVaccine;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class VaccineController extends Controller
{
    public function index(Request $request)
    {
        $query = ImmunizationVaccine::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $vaccines = $query->orderBy('name', 'asc')->paginate(10);

        return view('admin.vaccines.index', compact('vaccines'));
    }

    public function create()
    {
        return view('admin.vaccines.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:immunization_vaccines,code'],
            'name' => ['required', 'string', 'max:100'],
            'target_age_months' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $vaccine = ImmunizationVaccine::create([
            'code' => $request->code,
            'name' => $request->name,
            'target_age_months' => $request->target_age_months,
            'description' => $request->description,
        ]);

        ActivityLog::log('create_vaccine', "Created vaccine: {$vaccine->name} ({$vaccine->code})", auth()->id());

        return redirect()->route('admin.vaccines.index')->with('success', 'Vaksin berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $vaccine = ImmunizationVaccine::findOrFail($id);
        return view('admin.vaccines.edit', compact('vaccine'));
    }

    public function update(Request $request, $id)
    {
        $vaccine = ImmunizationVaccine::findOrFail($id);

        $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:immunization_vaccines,code,' . $vaccine->id],
            'name' => ['required', 'string', 'max:100'],
            'target_age_months' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $vaccine->update([
            'code' => $request->code,
            'name' => $request->name,
            'target_age_months' => $request->target_age_months,
            'description' => $request->description,
        ]);

        ActivityLog::log('update_vaccine', "Updated vaccine: {$vaccine->name} ({$vaccine->code})", auth()->id());

        return redirect()->route('admin.vaccines.index')->with('success', 'Vaksin berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $vaccine = ImmunizationVaccine::findOrFail($id);

        if ($vaccine->records()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus vaksin yang sedang digunakan dalam catatan imunisasi anak.');
        }

        $name = $vaccine->name;
        $code = $vaccine->code;
        $vaccine->delete();

        ActivityLog::log('delete_vaccine', "Deleted vaccine: {$name} ({$code})", auth()->id());

        return redirect()->route('admin.vaccines.index')->with('success', 'Vaksin berhasil dihapus.');
    }
}
