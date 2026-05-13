<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanionService extends Model
{
    protected $fillable = ['service_id', 'start_time', 'end_time', 'period'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
