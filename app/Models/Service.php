<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable =
        [
            'elder_id',
            'service_type',
            'service_condition',
            'service_address',
            'service_latitude',
            'service_longitude',
            'status',
        ];

    public function elder()
    {
        return $this->belongsTo(User::class, 'elder_id');
    }

    public function nurseService()
    {
        return $this->hasOne(NurseService::class, 'service_id');
    }

    public function companionService()
    {
        return $this->hasOne(CompanionService::class, 'service_id');
    }

    public function driverService()
    {
        return $this->hasOne(DriverService::class, 'service_id');
    }

    public function serviceAssignments()
    {
        return $this->hasMany(ServiceAssignment::class, 'service_id');
    }
}
