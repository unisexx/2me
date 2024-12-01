<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function getSearch(Request $request)
    {
                                                 // รับค่าคำค้นหาจาก Query Parameter
        $searchQuery = $request->input('q', ''); // ใช้ 'q' เป็นชื่อพารามิเตอร์สำหรับคำค้นหา

        if (!$searchQuery) {
            return response()->json([
                'status'  => 'error',
                'message' => 'กรุณาระบุคำค้นหา',
            ], 400);
        }

        // ค้นหาสติกเกอร์ด้วย Full-Text Search
        $stickers = DB::table('stickers')
            ->select('sticker_code', 'title_th', 'country', 'price', 'stickerresourcetype', 'version', 'created_at')
            ->whereRaw("MATCH(title_th, detail) AGAINST (? IN BOOLEAN MODE)", [$searchQuery . '*'])
            ->get()
            ->map(function ($sticker) {
                $createdAt = Carbon::parse($sticker->created_at);
                $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

                return [
                    'sticker_code' => $sticker->sticker_code,
                    'title_th'     => $sticker->title_th,
                    'country'      => $sticker->country,
                    'price'        => convertLineCoin2Money($sticker->price),
                    'img_url'      => getStickerImgUrl($sticker->stickerresourcetype, $sticker->version, $sticker->sticker_code),
                    'is_new'       => $isNew, // เพิ่มตัวแปร is_new
                ];
            });

        // ค้นหาธีมด้วย Full-Text Search
        $themes = DB::table('themes')
            ->select('id', 'theme_code', 'title', 'country', 'price', 'section', 'created_at') // กำหนดฟิลด์ที่ต้องการ
            ->whereRaw("MATCH(title, detail) AGAINST (? IN BOOLEAN MODE)", [$searchQuery . '*'])
            ->get()
            ->map(function ($theme) {
                $createdAt = Carbon::parse($theme->created_at);
                $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

                return [
                    'id'         => $theme->id,
                    'theme_code' => $theme->theme_code,
                    'title'      => $theme->title,
                    'country'    => $theme->country,
                    'price'      => convertLineCoin2Money($theme->price),
                    'img_url'    => generateThemeUrl($theme->theme_code, $theme->section, $theme->theme_code),
                    'is_new'     => $isNew,
                ];
            });

        // ค้นหาอิโมจิด้วย Full-Text Search
        $emojis = DB::table('emojis')
            ->select('id', 'emoji_code', 'title', 'country', 'price', 'created_at') // กำหนดฟิลด์ที่ต้องการ
            ->whereRaw("MATCH(title, detail) AGAINST (? IN BOOLEAN MODE)", [$searchQuery . '*'])
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
                    'is_new'     => $isNew,
                ];
            });

        // รวมผลลัพธ์จากทุกตาราง
        $results = [
            'stickers' => $stickers,
            'themes'   => $themes,
            'emojis'   => $emojis,
        ];

        // ส่งผลลัพธ์กลับไปยัง frontend
        return response()->json([
            'status' => 'success',
            'data'   => $results,
        ]);
    }

}
