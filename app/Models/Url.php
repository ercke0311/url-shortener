<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'long_url',
        'expired_at'
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];
}