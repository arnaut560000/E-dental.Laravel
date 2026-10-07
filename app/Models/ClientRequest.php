<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientRequest extends Model
{
    protected $guarded = [];

    protected $hidden = ['id_document_path'];

    protected $casts = [
        'birth_date' => 'date',
        'privacy_consent' => 'boolean',
        'consent_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];
}