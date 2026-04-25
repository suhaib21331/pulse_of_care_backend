<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequestAssignment extends Model
{
    protected $fillable = [
        'service_request_id',
        'provider_id',
        'provider_type',
        'distance_km',
        'matching_score',
        'status',
        'responded_at',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}
