<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProviderLocation extends Model
{
    use HasUuids;

    protected $fillable = ['provider_id', 'provider_type', 'latitude', 'longitude', 'is_available', 'last_seen_at'];

    public function provider()
    {
        return $this->morphTo();
    }
}
