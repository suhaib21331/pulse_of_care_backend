<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
     use HasUuids;
    public $table = 'family_members';

    public $primaryKey = 'id';
    
    public $incrementing = false;
    
    public $keyType = 'string';
    
    protected $fillable = [
        'user_id',
        'kinship',
        'elder_name',
        'elder_age',
        'city',
        'detailed_address',
        'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
