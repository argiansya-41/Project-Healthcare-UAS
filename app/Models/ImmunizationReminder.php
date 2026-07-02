<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImmunizationReminder extends Model
{
    protected $fillable = [
        'record_id',
        'parent_id',
        'send_date',
        'status',
        'channel',
    ];

    protected $casts = [
        'send_date' => 'date',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(ImmunizationRecord::class, 'record_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}
