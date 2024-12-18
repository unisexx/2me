<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sticker extends Model
{
    protected $table      = 'stickers';
    protected $primaryKey = 'id';
    protected $casts      = [
        'stamp' => 'array',
    ];
    protected $fillable = [
        'sticker_code',
        'category',
        'country',
        'title_th',
        'title_en',
        'author_th',
        'author_en',
        'detail',
        'credit',
        'price',
        'version',
        'onsale',
        'validdays',
        'hasanimation',
        'hassound',
        'stickerresourcetype',
        'status',
        'views_last_3_days',
        'stamp_start',
        'stamp_end',
        'stamp',
    ];
    public function promote()
    {
        return $this->hasOne('App\Models\Promote', 'product_code', 'sticker_code');
    }
}
