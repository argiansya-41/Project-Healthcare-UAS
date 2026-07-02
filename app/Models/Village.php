<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    protected $fillable = [
        'name',
        'kecamatan',
        'kabupaten',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function diseaseReports(): HasMany
    {
        return $this->hasMany(DiseaseReport::class, 'village_id');
    }
}
