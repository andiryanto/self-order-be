<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = [
        'user_id', 'specific_role'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    } //
}
