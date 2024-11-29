<?php

namespace App\Http\Controllers;

use App\Models\Emoji;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmojiController extends Controller
{
    public function getEmojiUpdate()
    {
        $emojiUpdate = Emoji::where('category', 'official')
            ->where('status', 1)
            ->where('created_at', '>', now()->subDays(7)->endOfDay())
            ->orderBy('id', 'desc')->get()
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
        $query = Emoji::where('status', 1);

        // กรองตาม category
        if (!empty($category)) {
            $query->where('category', $category);
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
            return [
                'id'         => $emoji->id,
                'emoji_code' => $emoji->emoji_code,
                'title'      => $emoji->title,
                'country'    => $emoji->country,
                'price'      => convertLineCoin2Money($emoji->price),
                'created_at' => $emoji->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json($emojis);
    }

    public function getEmojiView($id)
    {
        $emoji = Emoji::find($id);

        if (!$emoji) {
            return response()->json(['message' => 'Emoji not found'], 404);
        }

        $createdAt = Carbon::parse($emoji->created_at);
        $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

        // แปลง $emoji เป็น array และเพิ่มข้อมูลเพิ่มเติม
        $emojiData = array_merge(
            $emoji->toArray(),
            [
                'price'  => convertLineCoin2Money($emoji->price),
                'is_new' => $isNew,
            ]
        );

        // เก็บสถิติ views_last_3_days
        // ใช้ register_shutdown_function เพื่อบันทึกข้อมูลหลังจาก response
        register_shutdown_function(function () use ($emoji) {
            recordProductView('emoji', $emoji->id);
        });

        return response()->json($emojiData);
    }

    public function getEmojiSEO($id)
    {
        $emoji = Emoji::find($id);

        if (!$emoji) {
            return response()->json(['message' => 'Emoji not found'], 404);
        }

        return response()->json([
            'title'       => $emoji->title . ' - line2me',
            'description' => 'ซื้ออิโมจิไลน์ ' . $emoji->title . ' ในราคา ' . convertLineCoin2Money($emoji->price) . ' บาท พร้อมส่งฟรี',
            'keywords'    => 'อิโมจิไลน์, ' . $emoji->title . ', emoji shop',
            'image'       => 'https://stickershop.line-scdn.net/sticonshop/v1/product/' . $emoji->emoji_code . '/iphone/main.png',
            'url'         => url('/emoji/' . $emoji->id),
        ]);
    }

    // อิโมจิอื่นๆค้นหาตามชื่อผู้สร้าง
    public function getEmojiByAuthor(Request $request)
    {
        // คิวรี่สำหรับอัปเดตธีม
        $emojiByAuthor = Emoji::where('creator_name', $request->author)
            ->where('id', '!=', $request->id)
            ->where('country', $request->country)
            ->where('status', 1)
            ->inRandomOrder()
            ->take(8)
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

        return response()->json($emojiByAuthor);
    }

}
