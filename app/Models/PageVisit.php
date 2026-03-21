<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    protected $fillable = ['url', 'ip_address', 'user_agent', 'is_active'];
}
