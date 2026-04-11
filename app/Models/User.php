<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

#[Fillable(['email', 'password', 'full_name', 'phone_number', 'account_type', 'is_profile_completed'])]

#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable implements JWTSubject
{
    public $primaryKey = 'id';

    public $incrementing = false;

    public $keyType = 'string';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function nurse()
    {
        return $this->hasOne(Nurse::class);
    }

    public function companion()
    {
        return $this->hasOne(Companion::class);
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function elder()
    {
        return $this->hasOne(Elder::class);
    }

    public function familyMember()
    {
        return $this->hasOne(FamilyMember::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }
}
