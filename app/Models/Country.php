<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
