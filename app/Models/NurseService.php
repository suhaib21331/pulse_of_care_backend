<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NurseService extends Model
{
    protected $fillable = ['service_id', 'nurse_major'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
