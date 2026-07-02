<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Child extends Model
{
    protected $table = 'children';

    protected $fillable = [
        'parent_id',
        'name',
        'nik',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'birth_weight',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'birth_weight' => 'decimal:2',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function immunizationRecords(): HasMany
    {
        return $this->hasMany(ImmunizationRecord::class, 'child_id');
    }

    public function getAgeMonths(): int
    {
        return now()->diffInMonths($this->date_of_birth);
    }
}
