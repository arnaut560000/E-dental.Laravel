<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'birth_date' => 'date',
        'appointment_date' => 'date',
        'texted' => 'boolean',
        'called' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Pending', 'Approved']);
    }
}