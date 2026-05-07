<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderLocation extends Model
{
    protected $fillable = ['provider_id', 'provider_type', 'latitude', 'longitude', 'is_available', 'last_seen_at'];

    public function provider()
    {
        return $this->morphTo();
    }
}
