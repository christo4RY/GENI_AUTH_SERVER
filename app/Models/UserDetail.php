<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'firstname',
        'lastname',
        'avatar',
        'phone',
        'nrc',
        'country',
        'city',
        'address',
    ];

    public function getCityAttribute($value)
    {
        return ucwords($value);
    }

    public function getCountryAttribute($value)
    {
        return ucwords($value);
    }
}
