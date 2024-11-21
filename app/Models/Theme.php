<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $table = 'themes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'theme_code',
        'category',
        'country',
        'title',
        'author',
        'detail',
        'credit',
        'price',
        'slug',
        'status',
        'section',
        'ok',
        'views_last_3_days',
    ];

}
