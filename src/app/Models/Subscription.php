<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'city',
        'frequency',
        'confirmation_token',
        'confirmed_at',
        'unsubscribe_token',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'frequency' => 'string',
    ];

    public function isConfirmed(): bool
    {
        return $this->confirmed_at !== null;
    }
}
