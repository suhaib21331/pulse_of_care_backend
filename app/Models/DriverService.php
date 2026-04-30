<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverService extends Model
{
    protected $fillable =
        [
            'service_id',
            'pickup_address',
            'pickup_latitude',
            'pickup_longitude',
            'dropoff_address',
            'dropoff_latitude',
            'dropoff_longitude',
        ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
