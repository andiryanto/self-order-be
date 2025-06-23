<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    protected $fillable = [
        'user_id', 'phone'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }
}
