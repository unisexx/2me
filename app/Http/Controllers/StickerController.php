<?php

namespace App\Http\Controllers;

use App\Models\Sticker;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StickerController extends Controller
{
    public function getStickerUpdate()
    {
        // สติกเกอร์อัพเดทประจำสัปดาห์
        $stickerUpdate = Sticker::select('sticker_code', 'title_th', 'country', 'price', 'stickerresourcetype', 'version', 'created_at')
            ->where('category', 'official')
            ->where('status', 1)
            ->where('created_at', '>', now()->subDays(7)->endOfDay())
            ->orderByRaw("FIELD(country,'th','jp','tw','id') asc")
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
                    'created_at'   => $sticker->created_at->format('Y-m-d H:i:s'),
                    'is_new'       => $isNew, // เพิ่มตัวแปร is_new
                ];
            });

        return response()->json($stickerUpdate);
    }

    // หน้าลิสต์สติกเกอร์
    public function getStickerMore(Request $request)
    {
        $perPage = 48;

        // รับพารามิเตอร์จาก request
        $category = $request->input('category');
        $country  = $request->input('country');
        $order    = $request->input('order', 'new'); // ค่าเริ่มต้นคือ 'new'

        // สร้าง Query Builder
        $query = Sticker::select('sticker_code', 'title_th', 'country', 'price', 'stickerresourcetype', 'version', 'created_at')
            ->where('status', 1);

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
        $stickers = $query->simplePaginate($perPage);

        // แปลงข้อมูลเพิ่มเติม
        $stickers->getCollection()->transform(function ($sticker) {
            $createdAt = Carbon::parse($sticker->created_at);
            $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

            return [
                'sticker_code' => $sticker->sticker_code,
                'title_th'     => $sticker->title_th,
                'country'      => $sticker->country,
                'price'        => convertLineCoin2Money($sticker->price),
                'img_url'      => getStickerImgUrl($sticker->stickerresourcetype, $sticker->version, $sticker->sticker_code),
                'created_at'   => $sticker->created_at->format('Y-m-d H:i:s'),
                'is_new'       => $isNew,
            ];
        });

        return response()->json($stickers);
    }

    // หน้าวิวสติกเกอร์
    public function getStickerView($sticker_code)
    {
        $sticker = Sticker::where('sticker_code', $sticker_code)->first();

        if (!$sticker) {
            return response()->json(['message' => 'Sticker not found'], 404);
        }

        $createdAt = Carbon::parse($sticker->created_at);
        $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

        // แปลง $sticker เป็น array และเพิ่มข้อมูลเพิ่มเติม
        $stickerData = array_merge(
            $sticker->toArray(),
            [
                'img_url' => getStickerImgUrl($sticker->stickerresourcetype, $sticker->version, $sticker->sticker_code),
                'price'   => convertLineCoin2Money($sticker->price),
                'is_new'  => $isNew,
            ]
        );

        // เก็บสถิติ views_last_3_days
        // ใช้ register_shutdown_function เพื่อบันทึกข้อมูลหลังจาก response
        register_shutdown_function(function () use ($sticker) {
            recordProductView('sticker', $sticker->id);
        });

        return response()->json($stickerData);
    }

    // seo หน้าวิวสติกเกอร์
    public function getStickerSEO($sticker_code)
    {
        $sticker = Sticker::where('sticker_code', $sticker_code)->first();

        if (!$sticker) {
            return response()->json(['message' => 'Sticker not found'], 404);
        }

        return response()->json([
            'title'       => $sticker->title_th . ' - line2me',
            'description' => 'ซื้อสติกเกอร์ไลน์ ' . $sticker->title_th . ' ในราคา ' . convertLineCoin2Money($sticker->price) . ' บาท พร้อมส่งฟรี',
            'keywords'    => 'สติกเกอร์ไลน์, ' . $sticker->title_th . ', line2me sticker shop',
            'image'       => getStickerImgUrl($sticker->stickerresourcetype, $sticker->version, $sticker->sticker_code),
            'url'         => url('/sticker/' . $sticker->sticker_code),
        ]);
    }

    // สติกเกอร์อื่นๆค้นหาตามชื่อผู้สร้าง
    public function getStickerByAuthor(Request $request)
    {
        // คิวรี่สำหรับอัปเดตสติกเกอร์
        $stickerAuthor = Sticker::select('sticker_code', 'title_th', 'country', 'price', 'stickerresourcetype', 'version', 'created_at')
            ->where('author_th', $request->author_th)
            ->where('sticker_code', '!=', $request->sticker_code)
            ->where('country', $request->country)
            ->where('status', 1)
            ->inRandomOrder()
            ->take(10)
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
                    'created_at'   => $sticker->created_at->format('Y-m-d H:i:s'),
                    'is_new'       => $isNew,
                ];
            });

        // // คิวรี่สำหรับสุ่มสติกเกอร์
        // $stickerRandom = Sticker::select('sticker_code', 'title_th', 'country', 'price', 'stickerresourcetype', 'version', 'created_at')
        //     ->where('sticker_code', '!=', $request->sticker_code)
        //     ->where('country', $request->country)
        //     ->where('status', 1)
        //     ->orderByRaw('RAND()')
        //     ->take(6)
        //     ->get()
        //     ->map(function ($sticker) {
        //         $createdAt = Carbon::parse($sticker->created_at);
        //         $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

        //         return [
        //             'sticker_code' => $sticker->sticker_code,
        //             'title_th'     => $sticker->title_th,
        //             'country'      => $sticker->country,
        //             'price'        => convertLineCoin2Money($sticker->price),
        //             'img_url'      => getStickerImgUrl($sticker->stickerresourcetype, $sticker->version, $sticker->sticker_code),
        //             'created_at'   => $sticker->created_at->format('Y-m-d H:i:s'),
        //             'is_new'       => $isNew,
        //         ];
        //     });

        // // แปลงข้อมูลให้เป็นคอลเลกชันก่อนรวม
        // $result = collect($stickerUpdate)->merge(collect($stickerRandom));

        return response()->json($stickerAuthor);
    }

}
