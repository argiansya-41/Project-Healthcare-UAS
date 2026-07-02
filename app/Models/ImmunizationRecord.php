<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImmunizationRecord extends Model
{
    protected $fillable = [
        'child_id',
        'vaccine_id',
        'officer_id',
        'status',
        'scheduled_date',
        'administered_date',
        'batch_number',
        'notes',
        'vaccine_complaint',
        'doctor_response',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'administered_date' => 'date',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    public function vaccine(): BelongsTo
    {
        return $this->belongsTo(ImmunizationVaccine::class, 'vaccine_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(ImmunizationReminder::class, 'record_id');
    }
}
