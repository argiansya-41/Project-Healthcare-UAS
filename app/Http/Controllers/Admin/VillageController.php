<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    public function index(Request $request)
    {
        $query = Village::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('kecamatan', 'like', "%{$search}%")
                  ->orWhere('kabupaten', 'like', "%{$search}%");
        }

        $villages = $query->latest()->paginate(10);

        return view('admin.villages.index', compact('villages'));
    }

    public function create()
    {
        return view('admin.villages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'kabupaten' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $village = Village::create($request->all());

        ActivityLog::log('create_village', "Added village: {$village->name} (Kec. {$village->kecamatan}, Kab. {$village->kabupaten})", auth()->id());

        return redirect()->route('admin.villages.index')->with('success', 'Data wilayah/desa berhasil ditambahkan.');
    }

    public function edit(Village $village)
    {
        return view('admin.villages.edit', compact('village'));
    }

    public function update(Request $request, Village $village)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'kabupaten' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $village->update($request->all());

        ActivityLog::log('update_village', "Updated village data: {$village->name}", auth()->id());

        return redirect()->route('admin.villages.index')->with('success', 'Data wilayah/desa berhasil diperbarui.');
    }

    public function destroy(Village $village)
    {
        $name = $village->name;
        $village->delete();

        ActivityLog::log('delete_village', "Deleted village: {$name}", auth()->id());

        return redirect()->route('admin.villages.index')->with('success', 'Data wilayah/desa berhasil dihapus.');
    }
}
