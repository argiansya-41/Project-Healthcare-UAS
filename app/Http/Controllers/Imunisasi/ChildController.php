<?php

namespace App\Http\Controllers\Imunisasi;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function index(Request $request)
    {
        $query = Child::with('parent');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $children = $query->latest()->paginate(10);

        return view('imunisasi.children.index', compact('children'));
    }

    public function create()
    {
        // Get users who are warga (parents) to associate the child
        $parents = User::where('role', 'warga')->orderBy('name', 'asc')->get();
        return view('imunisasi.children.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'size:16', 'unique:children'],
            'gender' => ['required', 'in:L,P'],
            'date_of_birth' => ['required', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:100'],
            'birth_weight' => ['nullable', 'numeric', 'min:0'],
            'parent_id' => ['required', 'exists:users,id'],
        ]);

        $child = Child::create($request->all());

        ActivityLog::log('create_child', "Registered child: {$child->name} under parent {$child->parent->name}", auth()->id());

        return redirect()->route('imunisasi.children.index')->with('success', 'Data anak berhasil ditambahkan.');
    }

    public function edit(Child $child)
    {
        $parents = User::where('role', 'warga')->orderBy('name', 'asc')->get();
        return view('imunisasi.children.edit', compact('child', 'parents'));
    }

    public function update(Request $request, Child $child)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'size:16', 'unique:children,nik,'.$child->id],
            'gender' => ['required', 'in:L,P'],
            'date_of_birth' => ['required', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:100'],
            'birth_weight' => ['nullable', 'numeric', 'min:0'],
            'parent_id' => ['required', 'exists:users,id'],
        ]);

        $child->update($request->all());

        ActivityLog::log('update_child', "Updated child data: {$child->name}", auth()->id());

        return redirect()->route('imunisasi.children.index')->with('success', 'Data anak berhasil diperbarui.');
    }

    public function destroy(Child $child)
    {
        $name = $child->name;
        $child->delete();

        ActivityLog::log('delete_child', "Deleted child data for {$name}", auth()->id());

        return redirect()->route('imunisasi.children.index')->with('success', 'Data anak berhasil dihapus.');
    }

    // Citizen dashboard: List their own children
    public function myChildren()
    {
        $children = Child::where('parent_id', auth()->id())->latest()->get();
        return view('warga.children.index', compact('children'));
    }
}
