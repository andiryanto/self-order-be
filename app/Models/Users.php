<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Perbaiki use statement
use Illuminate\Database\Eloquent\Relations\HasOne;

class Users extends Authenticatable
{
    protected $fillable = [
        'email', 'name', 'password', 'role',
    ];

    protected $hidden = ['password'];

    // Relasi one-to-one
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }
    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class);
    }
    public function manager(): HasOne
    {
        return $this->hasOne(Manager::class);
    }
    public function owner(): HasOne
    {
        return $this->hasOne(Owner::class);
    }

    // Helper untuk ambil detail spesifik sesuai role
    public function detail()
    {
        return match ($this->role) {
            'customer' => $this->customer,
            'staff'    => $this->staff,
            'manager'  => $this->manager,
            'owner'    => $this->owner,
            default    => null,
        };
    }
}