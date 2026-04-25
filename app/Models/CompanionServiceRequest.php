<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanionServiceRequest extends Model
{
    protected $fillable = ['service_request_id', 'start_time', 'end_time', 'period'];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}
