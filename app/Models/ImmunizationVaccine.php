<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImmunizationVaccine extends Model
{
    protected $fillable = ['name', 'code', 'target_age_months', 'description'];

    public function records(): HasMany
    {
        return $this->hasMany(ImmunizationRecord::class, 'vaccine_id');
    }
}
