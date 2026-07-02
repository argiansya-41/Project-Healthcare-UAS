<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiseaseReport extends Model
{
    protected $fillable = [
        'reporter_id',
        'patient_name',
        'patient_nik',
        'patient_age',
        'patient_gender',
        'patient_address',
        'latitude',
        'longitude',
        'disease_type_id',
        'symptoms',
        'severity',
        'report_date',
        'status',
        'verified_by',
        'verification_notes',
        'treatment_recommendation',
        'village_id',
    ];

    protected $casts = [
        'report_date' => 'date',
        'patient_age' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function diseaseType(): BelongsTo
    {
        return $this->belongsTo(DiseaseType::class, 'disease_type_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id');
    }
}
