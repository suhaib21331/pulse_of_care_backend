<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasUuids;
    public $primaryKey = 'id';
    
    public $incrementing = false;
    
    public $keyType = 'string';
    
    protected $fillable = [
        'user_id',
        'driver_license_number',
        'car_license_number',
        'plate_number',
        'car_company',
        'car_type',
        'year_of_creation',
        'car_color',
        'car_image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
