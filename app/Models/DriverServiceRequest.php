<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverServiceRequest extends Model
{
    protected $fillable = 
    [
        'service_request_id',
        'pickup_address',
        'pickup_latitude',
        'pickup_longitude',
        'dropoff_address',
        'dropoff_latitude',
        'dropoff_longitude',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}
