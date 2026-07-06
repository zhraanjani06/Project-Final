<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $guarded = [];

    /**
     * Get the admin user who wrote this article.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the country this analysis relates to (if any).
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
}
