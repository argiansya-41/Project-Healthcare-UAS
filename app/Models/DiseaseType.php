<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiseaseType extends Model
{
    protected $fillable = ['code', 'name', 'description'];

    public function reports(): HasMany
    {
        return $this->hasMany(DiseaseReport::class, 'disease_type_id');
    }
}
