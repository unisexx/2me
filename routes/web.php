<?php

use App\Http\Controllers\StickerController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/{any}', function () {
    return view('app'); // เรียก view หลักที่จะใช้ Vue.js
})->where('any', '.*');
