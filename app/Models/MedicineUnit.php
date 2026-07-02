<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicineUnit extends Model
{
    protected $fillable = ['name', 'abbreviation'];

    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class, 'unit_id');
    }
}
