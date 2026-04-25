<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NurseServiceRequest extends Model
{
    protected $fillable = ['service_request_id', 'nurse_major'];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}
