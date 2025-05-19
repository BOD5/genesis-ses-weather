<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherHistory extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'city',
        'temperature',
        'humidity',
        'description',
        'icon',
        'raw_data',
        'recorded_at',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'recorded_at' => 'datetime',
        'temperature' => 'float',
        'humidity' => 'integer',
    ];
}
