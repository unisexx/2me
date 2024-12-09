<?php

use App\Http\Controllers\CrawlerController;
use App\Http\Controllers\EmojiController;
use App\Http\Controllers\PageController;
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
Route::get('/sticker-official-thai', [StickerController::class, 'getStickerOfficialThai']);
Route::get('/sticker-official-oversea', [StickerController::class, 'getStickerOfficialOversea']);
Route::get('/sticker-creator-thai', [StickerController::class, 'getStickerCreatorThai']);
Route::get('/sticker-creator-oversea', [StickerController::class, 'getStickerCreatorOversea']);
Route::get('/sticker-more', [StickerController::class, 'getStickerMore']);
Route::get('/sticker-view/{sticker_code}', [StickerController::class, 'getStickerView']);
Route::get('/sticker-by-author', [StickerController::class, 'getStickerByAuthor']);

// Theme
Route::get('/theme-update', [ThemeController::class, 'getThemeUpdate']);
Route::get('/theme-official-thai', [ThemeController::class, 'getThemeOfficialThai']);
Route::get('/theme-official-oversea', [ThemeController::class, 'getThemeOfficialOversea']);
Route::get('/theme-creator-thai', [ThemeController::class, 'getThemeCreatorThai']);
Route::get('/theme-creator-oversea', [ThemeController::class, 'getThemeCreatorOversea']);
Route::get('/theme-more', [ThemeController::class, 'getThemeMore']);
Route::get('/theme-view/{id}', [ThemeController::class, 'getThemeView']);
Route::get('/theme-by-author', [ThemeController::class, 'getThemeByAuthor']);

// Emoji
Route::get('/emoji-update', [EmojiController::class, 'getEmojiUpdate']);
Route::get('/emoji-official-thai', [EmojiController::class, 'getEmojiOfficialThai']);
Route::get('/emoji-official-oversea', [EmojiController::class, 'getEmojiOfficialOversea']);
Route::get('/emoji-creator-thai', [EmojiController::class, 'getEmojiCreatorThai']);
Route::get('/emoji-creator-oversea', [EmojiController::class, 'getEmojiCreatorOversea']);
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

// Crawer
// Route::get('getsticker/{sticker_code}', [CrawlerController::class, 'getsticker']);
// Route::get('gettheme/{theme_code}', [CrawlerController::class, 'gettheme']);
// Route::get('getemoji/{emoji_code}', [CrawlerController::class, 'getemoji']);

// Route::get('/getstickerstore/{type}/{category}/{page}', [CrawlerController::class, 'getstickerstore']);

// Page
// Route::get('/page/{id}', [PageController::class, 'getPage']);
