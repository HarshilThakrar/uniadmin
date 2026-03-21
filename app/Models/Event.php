<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'image' => 'array',
    ];
}
