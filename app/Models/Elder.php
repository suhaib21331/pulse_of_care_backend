<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;


use Illuminate\Database\Eloquent\Model;

class Elder extends Model
{
     use HasUuids;
    public $table = 'elderlies';

    public $primaryKey = 'id';
    
    public $incrementing = false;
    
    public $keyType = 'string';
    
    protected $fillable = [
        'user_id',
        'age',
        'gender',
        'chronic_diseases',
        'current_medications',
        'allergies',
        'uses_diapers',
        'movement_level',
        'need_wheel_chair',
        'city',
        'detailed_address',
        'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
