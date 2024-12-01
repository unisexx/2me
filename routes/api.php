<?php

use App\Http\Controllers\EmojiController;
use App\Http\Controllers\ProductViewController;
use App\Http\Controllers\PromoteController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StickerController;
use App\Http\Controllers\ThemeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Promote
Route::get('/promote-sticker', [PromoteController::class, 'getPromoteSticker']);

// Sticker
Route::get('/sticker-update', [StickerController::class, 'getStickerUpdate']);
Route::get('/sticker-more', [StickerController::class, 'getStickerMore']);
Route::get('/sticker-view/{sticker_code}', [StickerController::class, 'getStickerView']);
Route::get('/sticker-by-author', [StickerController::class, 'getStickerByAuthor']);

// Theme
Route::get('/theme-update', [ThemeController::class, 'getThemeUpdate']);
Route::get('/theme-more', [ThemeController::class, 'getThemeMore']);
Route::get('/theme-view/{id}', [ThemeController::class, 'getThemeView']);
Route::get('/theme-by-author', [ThemeController::class, 'getThemeByAuthor']);

// Emoji
Route::get('/emoji-update', [EmojiController::class, 'getEmojiUpdate']);
Route::get('/emoji-more', [EmojiController::class, 'getEmojiMore']);
Route::get('/emoji-view/{id}', [EmojiController::class, 'getEmojiView']);
Route::get('/emoji-by-author', [EmojiController::class, 'getEmojiByAuthor']);

// SEO
Route::get('/sticker-seo/{sticker_code}', [StickerController::class, 'getStickerSEO']);
Route::get('/theme-seo/{id}', [ThemeController::class, 'getThemeSEO']);
Route::get('/emoji-seo/{id}', [EmojiController::class, 'getEmojiSEO']);

// ProductView Log
Route::post('/record-product-view', [ProductViewController::class, 'recordView']);

// Search
Route::get('/search', [SearchController::class, 'getSearch']);
