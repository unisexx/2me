<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emoji extends Model
{
    protected $table      = 'emojis';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'emoji_code',
        'title',
        'detail',
        'creator_name',
        'threedays',
        'created',
        'updated',
        'category',
        'country',
        'slug',
        'price',
        'status',
        'views_last_3_days',
    ];

}
