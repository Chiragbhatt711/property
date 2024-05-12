<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'adress',
        'city',
        'description',
        'price',
        'property_type',
        'status',
        'parking_two_wheel',
        'parking_for_wheel',
        'electricity',
        'furniture',
        'other_electric_accessories',
        'client_name',
        'client_phone',
        'promoted',
        'verified',
        'bhk',
    ];
}
