<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table      = 'pages';
    protected $primaryKey = 'id';
    protected $fillable   = [
        'title',
        'detail',
        'created',
        'status',
        'user_id',
        'category',
    ];

}
