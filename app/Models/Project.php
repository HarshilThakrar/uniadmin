<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'city_name', 'state_name', 'description', 'image', 'is_active', 'sort_order'];
}
