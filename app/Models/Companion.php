<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Companion extends Model
{
    use HasUuids;
    public $primaryKey = 'id';
    
    public $incrementing = false;
    
    public $keyType = 'string';
    
    protected $fillable = [
        'user_id',
        'skills',
        'years_of_experience',
        'availability',
        'certificates',
        'biometric'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
