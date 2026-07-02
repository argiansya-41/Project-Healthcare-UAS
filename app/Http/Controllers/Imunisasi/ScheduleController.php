<?php

namespace App\Http\Controllers\Imunisasi;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\ImmunizationVaccine;
use App\Models\ImmunizationRecord;
use App\Models\ImmunizationReminder;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = ImmunizationRecord::with(['child', 'vaccine', 'officer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('child', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $records = $query->orderBy('scheduled_date', 'asc')->paginate(10);

        return view('imunisasi.schedules.index', compact('records'));
    }

    public function create()
    {
        $children = Child::orderBy('name', 'asc')->get();
        $vaccines = ImmunizationVaccine::orderBy('target_age_months', 'asc')->get();
        return view('imunisasi.schedules.create', compact('children', 'vaccines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'child_id' => ['required', 'exists:children,id'],
            'vaccine_id' => ['required', 'exists:immunization_vaccines,id'],
            'status' => ['required', 'in:scheduled,completed,missed'],
            'scheduled_date' => ['required', 'date'],
            'administered_date' => ['required_if:status,completed', 'nullable', 'date'],
            'batch_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $record = ImmunizationRecord::create([
            'child_id' => $request->child_id,
            'vaccine_id' => $request->vaccine_id,
            'status' => $request->status,
            'scheduled_date' => $request->scheduled_date,
            'administered_date' => $request->status === 'completed' ? $request->administered_date : null,
            'batch_number' => $request->batch_number,
            'notes' => $request->notes,
            'officer_id' => $request->status === 'completed' ? auth()->id() : null,
        ]);

        $child = Child::find($request->child_id);
        $childName = $child->name;
        $vaccineName = ImmunizationVaccine::find($request->vaccine_id)->name;

        if ($request->status === 'scheduled') {
            $scheduledDate = \Carbon\Carbon::parse($request->scheduled_date);
            $daysDiff = now()->startOfDay()->diffInDays($scheduledDate, false);
            if ($daysDiff < 5) {
                $sendDate = $scheduledDate;
            } else {
                $sendDate = $scheduledDate->copy()->subDays(5);
            }

            ImmunizationReminder::create([
                'record_id' => $record->id,
                'parent_id' => $child->parent_id,
                'send_date' => $sendDate->format('Y-m-d'),
                'status' => 'pending',
                'channel' => 'dashboard',
            ]);
        }

        ActivityLog::log('schedule_immunization', "Scheduled/recorded immunization for {$childName} ({$vaccineName}) - Status: {$request->status}", auth()->id());

        return redirect()->route('imunisasi.schedules.index')->with('success', 'Jadwal/Catatan imunisasi berhasil disimpan.');
    }

    public function edit($id)
    {
        $record = ImmunizationRecord::findOrFail($id);
        $children = Child::orderBy('name', 'asc')->get();
        $vaccines = ImmunizationVaccine::orderBy('target_age_months', 'asc')->get();
        return view('imunisasi.schedules.edit', compact('record', 'children', 'vaccines'));
    }

    public function update(Request $request, $id)
    {
        $record = ImmunizationRecord::findOrFail($id);

        $request->validate([
            'status' => ['required', 'in:scheduled,completed,missed'],
            'scheduled_date' => ['required', 'date'],
            'administered_date' => ['required_if:status,completed', 'nullable', 'date'],
            'batch_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $record->update([
            'status' => $request->status,
            'scheduled_date' => $request->scheduled_date,
            'administered_date' => $request->status === 'completed' ? $request->administered_date : null,
            'batch_number' => $request->batch_number,
            'notes' => $request->notes,
            'officer_id' => $request->status === 'completed' ? auth()->id() : $record->officer_id,
        ]);

        if ($request->status === 'scheduled') {
            $scheduledDate = \Carbon\Carbon::parse($request->scheduled_date);
            $daysDiff = now()->startOfDay()->diffInDays($scheduledDate, false);
            if ($daysDiff < 5) {
                $sendDate = $scheduledDate;
            } else {
                $sendDate = $scheduledDate->copy()->subDays(5);
            }

            ImmunizationReminder::updateOrCreate(
                ['record_id' => $record->id],
                [
                    'parent_id' => $record->child->parent_id,
                    'send_date' => $sendDate->format('Y-m-d'),
                    'status' => 'pending',
                    'channel' => 'dashboard',
                ]
            );
        } else {
            ImmunizationReminder::where('record_id', $record->id)->delete();
        }

        ActivityLog::log('update_immunization_record', "Updated immunization record #{$record->id} for {$record->child->name}", auth()->id());

        return redirect()->route('imunisasi.schedules.index')->with('success', 'Catatan imunisasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $record = ImmunizationRecord::findOrFail($id);
        $name = $record->child->name;
        $record->delete();

        ActivityLog::log('delete_immunization_record', "Deleted immunization record #{$id} for {$name}", auth()->id());

        return redirect()->route('imunisasi.schedules.index')->with('success', 'Catatan imunisasi berhasil dihapus.');
    }

    // Citizen: View vaccination history for a specific child
    public function history($child_id)
    {
        $child = Child::findOrFail($child_id);

        // Security check: Make sure parent owns the child
        if (auth()->user()->role === 'warga' && $child->parent_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat data anak ini.');
        }

        $records = ImmunizationRecord::with(['vaccine', 'officer'])
            ->where('child_id', $child_id)
            ->orderBy('scheduled_date', 'asc')
            ->get();

        return view('warga.children.history', compact('child', 'records'));
    }

    public function reportComplaint(Request $request, $id)
    {
        $record = ImmunizationRecord::with('child')->findOrFail($id);

        if (auth()->user()->role === 'warga' && $record->child->parent_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melaporkan keluhan untuk catatan ini.');
        }

        $request->validate([
            'vaccine_complaint' => ['required', 'string'],
        ]);

        $record->update([
            'vaccine_complaint' => $request->vaccine_complaint,
            'doctor_response' => null,
        ]);

        ActivityLog::log('report_vaccine_complaint', "Reported vaccine complaint for child {$record->child->name} (Record #{$record->id})", auth()->id());

        return back()->with('success', 'Keluhan pasca imunisasi berhasil dilaporkan.');
    }
}
