<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_number',
        'payment_url',
        'payment_response',
        'status',
    ];

    protected $casts = [
        'payment_response' => AsArrayObject::class,
    ];
}
