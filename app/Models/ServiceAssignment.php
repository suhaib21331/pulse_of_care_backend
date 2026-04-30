<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAssignment extends Model
{
    protected $fillable = [
        'service_id',
        'provider_id',
        'provider_type',
        'distance_km',
        'matching_score',
        'status',
        'responded_at',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
