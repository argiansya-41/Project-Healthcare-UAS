<?php

namespace App\Http\Controllers\Imunisasi;

use App\Http\Controllers\Controller;
use App\Models\ImmunizationReminder;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $query = ImmunizationReminder::with(['record.child', 'record.vaccine', 'parent']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reminders = $query->latest()->paginate(10);

        return view('imunisasi.reminders.index', compact('reminders'));
    }

    public function send($id)
    {
        $reminder = ImmunizationReminder::findOrFail($id);

        if ($reminder->status === 'sent') {
            return back()->with('error', 'Reminder ini sudah terkirim sebelumnya.');
        }

        // Simulate sending via Gateway (WhatsApp API, SMS, or Email)
        // Here we just mark it as sent
        $reminder->update([
            'status' => 'sent',
            'updated_at' => now(),
        ]);

        $childName = $reminder->record->child->name;
        $vaccineName = $reminder->record->vaccine->name;

        ActivityLog::log('send_reminder', "Sent immunization reminder (#{$reminder->id}) for {$childName} ({$vaccineName}) via {$reminder->channel}", auth()->id());

        return redirect()->route('imunisasi.reminders.index')->with('success', "Reminder untuk anak {$childName} berhasil dikirim.");
    }
}
