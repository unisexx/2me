<?php

namespace App\Http\Controllers;

use App\Models\Emoji;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EmojiController extends Controller
{
    public function getEmojiUpdate()
    {
        // ใช้ Cache เป็นเวลา 3600 วินาที (1 ชั่วโมง)
        $emojiUpdate = Cache::remember('emoji_update', now()->addSeconds(3600), function () {
            return Emoji::select('id', 'emoji_code', 'title', 'country', 'price', 'created_at')
                ->where('category', 'official')
                ->where('status', 1)
                ->where('created_at', '>', now()->subDays(7)->endOfDay())
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($emoji) {
                    $createdAt = Carbon::parse($emoji->created_at);
                    $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

                    return [
                        'id'         => $emoji->id,
                        'emoji_code' => $emoji->emoji_code,
                        'title'      => $emoji->title,
                        'country'    => $emoji->country,
                        'price'      => convertLineCoin2Money($emoji->price),
                        'created_at' => $emoji->created_at->format('Y-m-d H:i:s'),
                        'is_new'     => $isNew,
                    ];
                });
        });

        return response()->json($emojiUpdate);
    }

    public function getEmojiOfficialThai()
    {
        // ใช้ Cache เป็นเวลา 3600 วินาที (1 ชั่วโมง)
        $emojiUpdate = Cache::remember('emoji_official_thai', now()->addSeconds(3600), function () {
            return Emoji::select('id', 'emoji_code', 'title', 'country', 'price', 'created_at')
                ->where('category', 'official')
                ->where('status', 1)
                ->where('country', 'th')
                ->orderBy('views_last_3_days', 'desc')
                ->take(16)
                ->get()
                ->map(function ($emoji) {
                    $createdAt = Carbon::parse($emoji->created_at);
                    $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

                    return [
                        'id'         => $emoji->id,
                        'emoji_code' => $emoji->emoji_code,
                        'title'      => $emoji->title,
                        'country'    => $emoji->country,
                        'price'      => convertLineCoin2Money($emoji->price),
                        'created_at' => $emoji->created_at->format('Y-m-d H:i:s'),
                        'is_new'     => $isNew,
                    ];
                });
        });

        return response()->json($emojiUpdate);
    }

    public function getEmojiOfficialOversea()
    {
        // ใช้ Cache เป็นเวลา 3600 วินาที (1 ชั่วโมง)
        $emojiUpdate = Cache::remember('emoji_official_oversea', now()->addSeconds(3600), function () {
            return Emoji::select('id', 'emoji_code', 'title', 'country', 'price', 'created_at')
                ->where('category', 'official')
                ->where('status', 1)
                ->whereIn('country', ['jp', 'id', 'us', 'kr', 'es', 'in', 'tw', 'cn', 'br', 'my', 'ph', 'mx', 'hk'])
                ->orderBy('views_last_3_days', 'desc')
                ->take(16)
                ->get()
                ->map(function ($emoji) {
                    $createdAt = Carbon::parse($emoji->created_at);
                    $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

                    return [
                        'id'         => $emoji->id,
                        'emoji_code' => $emoji->emoji_code,
                        'title'      => $emoji->title,
                        'country'    => $emoji->country,
                        'price'      => convertLineCoin2Money($emoji->price),
                        'created_at' => $emoji->created_at->format('Y-m-d H:i:s'),
                        'is_new'     => $isNew,
                    ];
                });
        });

        return response()->json($emojiUpdate);
    }

    public function getEmojiCreatorThai()
    {
        // ใช้ Cache เป็นเวลา 3600 วินาที (1 ชั่วโมง)
        $emojiUpdate = Cache::remember('emoji_creator_thai', now()->addSeconds(3600), function () {
            return Emoji::select('id', 'emoji_code', 'title', 'country', 'price', 'created_at')
                ->where('category', 'creator')
                ->where('status', 1)
                ->where('country', 'th')
                ->orderBy('views_last_3_days', 'desc')
                ->take(16)
                ->get()
                ->map(function ($emoji) {
                    $createdAt = Carbon::parse($emoji->created_at);
                    $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

                    return [
                        'id'         => $emoji->id,
                        'emoji_code' => $emoji->emoji_code,
                        'title'      => $emoji->title,
                        'country'    => $emoji->country,
                        'price'      => convertLineCoin2Money($emoji->price),
                        'created_at' => $emoji->created_at->format('Y-m-d H:i:s'),
                        'is_new'     => $isNew,
                    ];
                });
        });

        return response()->json($emojiUpdate);
    }

    public function getEmojiCreatorOversea()
    {
        // ใช้ Cache เป็นเวลา 3600 วินาที (1 ชั่วโมง)
        $emojiUpdate = Cache::remember('emoji_creator_oversea', now()->addSeconds(3600), function () {
            return Emoji::select('id', 'emoji_code', 'title', 'country', 'price', 'created_at')
                ->where('category', 'creator')
                ->where('status', 1)
                ->whereIn('country', ['jp', 'id', 'us', 'kr', 'es', 'in', 'tw', 'cn', 'br', 'my', 'ph', 'mx', 'hk'])
                ->orderBy('views_last_3_days', 'desc')
                ->take(16)
                ->get()
                ->map(function ($emoji) {
                    $createdAt = Carbon::parse($emoji->created_at);
                    $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

                    return [
                        'id'         => $emoji->id,
                        'emoji_code' => $emoji->emoji_code,
                        'title'      => $emoji->title,
                        'country'    => $emoji->country,
                        'price'      => convertLineCoin2Money($emoji->price),
                        'created_at' => $emoji->created_at->format('Y-m-d H:i:s'),
                        'is_new'     => $isNew,
                    ];
                });
        });

        return response()->json($emojiUpdate);
    }

    public function getEmojiMore(Request $request)
    {
        $perPage = 48;

        // รับพารามิเตอร์จาก request
        $category = $request->input('category');
        $country  = $request->input('country');
        $order    = $request->input('order', 'new'); // ค่าเริ่มต้นคือ 'new'

        // สร้าง Query Builder
        $query = Emoji::select('id', 'emoji_code', 'title', 'country', 'price', 'created_at')->where('status', 1);

        // กรองตาม category
        if (!empty($category)) {
            if ($category === 'official') {
                $query->where('category', 'official');
            } else {
                $query->where('category', '!=', 'official');
            }
        }

        // กรองตาม country
        if (!empty($country)) {
            if ($country === 'oversea') {
                $query->where('country', '!=', 'th'); // กรอง country ที่ไม่ใช่ 'th'
            } else {
                $query->where('country', $country); // กรองตาม country ที่ระบุ
            }
        }

        // จัดเรียงข้อมูล
        if ($order === 'popular') {
            $query->orderBy('views_last_3_days', 'desc');
        } else {
            $query->orderBy('id', 'desc'); // ค่าเริ่มต้นคือ new
        }

        // ใช้ simplePaginate สำหรับ Infinity Scroll
        $emojis = $query->simplePaginate($perPage);

        // แปลงข้อมูลเพิ่มเติม
        $emojis->getCollection()->transform(function ($emoji) {
            $createdAt = Carbon::parse($emoji->created_at);
            $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

            return [
                'id'         => $emoji->id,
                'emoji_code' => $emoji->emoji_code,
                'title'      => $emoji->title,
                'country'    => $emoji->country,
                'price'      => convertLineCoin2Money($emoji->price),
                'created_at' => $emoji->created_at->format('Y-m-d H:i:s'),
                'is_new'     => $isNew,
            ];
        });

        return response()->json($emojis);
    }

    public function getEmojiView($id)
    {
        // ใช้ Cache เป็นเวลา 1 วัน (1440 นาที)
        $cacheKey = "emoji_view_{$id}";

        $emojiData = Cache::remember($cacheKey, 1440, function () use ($id) {
            $emoji = Emoji::find($id);

            if (!$emoji) {
                return null; // หากไม่มี Emoji ให้คืนค่า null ใน Cache
            }

            $createdAt = Carbon::parse($emoji->created_at);
            $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

            // แปลง $emoji เป็น array และเพิ่มข้อมูลเพิ่มเติม
            return array_merge(
                $emoji->toArray(),
                [
                    'price'  => convertLineCoin2Money($emoji->price),
                    'is_new' => $isNew,
                ]
            );
        });

        // หากไม่มี Emoji ใน Cache ให้ส่ง HTTP 404
        if (!$emojiData) {
            return response()->json(['message' => 'Emoji not found'], 404);
        }

        return response()->json($emojiData);
    }

    public function getEmojiSEO($id)
    {
        // ใช้ Cache เป็นเวลา 1 วัน (1440 นาที)
        $cacheKey = "emoji_seo_{$id}";

        $seoData = Cache::remember($cacheKey, 1440, function () use ($id) {
            $emoji = Emoji::find($id);

            if (!$emoji) {
                return null; // หากไม่มี Emoji ให้เก็บค่า null ใน Cache
            }

            return [
                'title'       => $emoji->title . ' - line2me',
                'description' => 'ซื้ออิโมจิไลน์ ' . $emoji->title . ' ในราคา ' . convertLineCoin2Money($emoji->price) . ' บาท พร้อมส่งฟรี',
                'keywords'    => 'อิโมจิไลน์, ' . $emoji->title . ', emoji shop',
                'image'       => 'https://stickershop.line-scdn.net/sticonshop/v1/product/' . $emoji->emoji_code . '/iphone/main.png',
                'url'         => url('/emoji/' . $emoji->id),
            ];
        });

        // หากไม่มีข้อมูล SEO ใน Cache ให้ส่ง HTTP 404
        if (!$seoData) {
            return response()->json(['message' => 'Emoji not found'], 404);
        }

        return response()->json($seoData);
    }

    // อิโมจิอื่นๆค้นหาตามชื่อผู้สร้าง
    public function getEmojiByAuthor(Request $request)
    {
        // ใช้ Raw SQL สำหรับ Subquery
        $emojiByAuthor = DB::select("
            SELECT `id`, `emoji_code`, `title`, `country`, `price`, `created_at`
            FROM `emojis`
            WHERE `creator_name` = ?
            AND `id` < ?
            AND `country` = ?
            AND `status` = 1
            ORDER BY `id`
            LIMIT 8
        ", [
            $request->creator_name,
            $request->id,
            $request->country,
        ]);

        // แปลงข้อมูล
        $emojiByAuthor = collect($emojiByAuthor)->map(function ($emoji) {
            $createdAt = Carbon::parse($emoji->created_at);
            $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

            return [
                'id'         => $emoji->id,
                'emoji_code' => $emoji->emoji_code,
                'title'      => $emoji->title,
                'country'    => $emoji->country,
                'price'      => convertLineCoin2Money($emoji->price),
                'created_at' => $createdAt->format('Y-m-d H:i:s'),
                'is_new'     => $isNew,
            ];
        });

        return response()->json($emojiByAuthor);
    }

}
