<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\ImmunizationRecord;
use Illuminate\Http\Request;

class WargaComplaintController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $children = Child::where('parent_id', $user->id)->get();
        $childIds = $children->pluck('id');

        $complaints = ImmunizationRecord::with(['child', 'vaccine'])
            ->whereIn('child_id', $childIds)
            ->where('status', 'completed')
            ->orderBy('administered_date', 'desc')
            ->paginate(10);

        return view('warga.complaints.index', compact('complaints'));
    }
}
