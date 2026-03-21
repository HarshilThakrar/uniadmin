<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqQuestion extends Model
{
    protected $fillable = ['name', 'email', 'message'];
}
