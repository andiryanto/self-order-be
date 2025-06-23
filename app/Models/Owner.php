<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $fillable = [
        'user_id', 'level'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    } //
}
