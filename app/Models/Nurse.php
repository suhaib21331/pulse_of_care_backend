<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Nurse extends Model
{
    use HasUuids;
    public $primaryKey = 'id';

    public $incrementing = false;

    public $keyType = 'string';

    protected $fillable = [
        'user_id',
        'major',
        'years_of_experience',
        'license_number',
        'work_place',
        'about_you',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->morphOne(ProviderLocation::class, 'provider');
    }
}
