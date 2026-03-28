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

#[Fillable(['email', 'password', 'full_name', 'phone_number', 'account_type'])]

#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
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

}
