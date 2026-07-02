<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiseaseType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DiseaseTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = DiseaseType::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $diseaseTypes = $query->orderBy('name', 'asc')->paginate(10);

        return view('admin.disease_types.index', compact('diseaseTypes'));
    }

    public function create()
    {
        return view('admin.disease_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:disease_types,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $diseaseType = DiseaseType::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'description' => $request->description,
        ]);

        ActivityLog::log('create_disease_type', "Created disease type: {$diseaseType->name} ({$diseaseType->code})", auth()->id());

        return redirect()->route('admin.disease-types.index')->with('success', 'Jenis penyakit berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $diseaseType = DiseaseType::findOrFail($id);
        return view('admin.disease_types.edit', compact('diseaseType'));
    }

    public function update(Request $request, $id)
    {
        $diseaseType = DiseaseType::findOrFail($id);

        $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:disease_types,code,' . $diseaseType->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $diseaseType->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'description' => $request->description,
        ]);

        ActivityLog::log('update_disease_type', "Updated disease type: {$diseaseType->name} ({$diseaseType->code})", auth()->id());

        return redirect()->route('admin.disease-types.index')->with('success', 'Jenis penyakit berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $diseaseType = DiseaseType::findOrFail($id);

        if ($diseaseType->reports()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus jenis penyakit yang sedang digunakan dalam laporan kasus.');
        }

        $name = $diseaseType->name;
        $code = $diseaseType->code;
        $diseaseType->delete();

        ActivityLog::log('delete_disease_type', "Deleted disease type: {$name} ({$code})", auth()->id());

        return redirect()->route('admin.disease-types.index')->with('success', 'Jenis penyakit berhasil dihapus.');
    }
}
