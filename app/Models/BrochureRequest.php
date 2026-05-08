<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrochureRequest extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'company', 'brochure_path'];
}
