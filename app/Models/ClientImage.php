<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientImage extends Model
{
    protected $fillable = ['image', 'is_active', 'sort_order'];
}
