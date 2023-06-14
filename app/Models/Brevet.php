<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brevet extends Model
{
    use HasFactory;

    protected $fillable = [
        'nro',
        'expiration_date',
        'broadcast_date',
        'category',
        'photo'
    ];
}
