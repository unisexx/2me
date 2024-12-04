<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ThemeController extends Controller
{
    public function getThemeUpdate()
    {
        // ใช้ Cache เป็นเวลา 3600 วินาที (1 ชั่วโมง)
        $themeUpdate = Cache::remember('theme_update', 3600, function () {
            return Theme::select('id', 'theme_code', 'title', 'country', 'price', 'section', 'created_at')
                ->where('category', 'official')
                ->where('status', 1)
                ->where('created_at', '>', now()->subDays(7)->endOfDay())
                ->orderBy('id', 'desc')
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
                        'created_at' => $theme->created_at->format('Y-m-d H:i:s'),
                        'is_new'     => $isNew,
                    ];
                });
        });

        return response()->json($themeUpdate);
    }

    public function getThemeMore(Request $request)
    {
        $perPage = 48;

        // รับพารามิเตอร์จาก request
        $category = $request->input('category');
        $country  = $request->input('country');
        $order    = $request->input('order', 'new'); // ค่าเริ่มต้นคือ 'new'

        // สร้าง Query Builder
        $query = Theme::select('id', 'theme_code', 'title', 'country', 'price', 'section', 'created_at')->where('status', 1);

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
        $themes = $query->simplePaginate($perPage);

        // แปลงข้อมูลเพิ่มเติม
        $themes->getCollection()->transform(function ($theme) {

            $createdAt = Carbon::parse($theme->created_at);
            $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

            return [
                'id'         => $theme->id,
                'theme_code' => $theme->theme_code,
                'title'      => $theme->title,
                'country'    => $theme->country,
                'price'      => convertLineCoin2Money($theme->price),
                'img_url'    => generateThemeUrl($theme->theme_code, $theme->section, $theme->theme_code),
                'created_at' => $theme->created_at->format('Y-m-d H:i:s'),
                'is_new'     => $isNew,
            ];
        });

        return response()->json($themes);
    }

    public function getThemeView($id)
    {
        // ใช้ Cache เป็นเวลา 1 วัน (1440 นาที)
        $cacheKey = "theme_view_{$id}";

        $themeData = Cache::remember($cacheKey, 1440, function () use ($id) {
            $theme = Theme::find($id);

            if (!$theme) {
                return null; // หากไม่มีข้อมูล Theme จะเก็บค่า null ใน Cache
            }

            $createdAt = Carbon::parse($theme->created_at);
            $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

            // แปลง $theme เป็น array และเพิ่มข้อมูลเพิ่มเติม
            return array_merge(
                $theme->toArray(),
                [
                    'img_url' => generateThemeUrl($theme->theme_code, $theme->section, $theme->theme_code),
                    'price'   => convertLineCoin2Money($theme->price),
                    'is_new'  => $isNew,
                ]
            );
        });

        // หากไม่มีข้อมูล Theme ใน Cache ให้ส่ง HTTP 404
        if (!$themeData) {
            return response()->json(['message' => 'Theme not found'], 404);
        }

        return response()->json($themeData);
    }

    public function getThemeSEO($id)
    {
        // ใช้ Cache เป็นเวลา 1 วัน (1440 นาที)
        $cacheKey = "theme_seo_{$id}";

        $seoData = Cache::remember($cacheKey, 1440, function () use ($id) {
            $theme = Theme::find($id);

            if (!$theme) {
                return null; // หากไม่มี Theme ให้เก็บค่า null ใน Cache
            }

            return [
                'title'       => $theme->title . ' - line2me',
                'description' => 'ซื้อธีมไลน์ ' . $theme->title . ' ในราคา ' . convertLineCoin2Money($theme->price) . ' บาท พร้อมส่งฟรี',
                'keywords'    => 'ธีมไลน์, ' . $theme->title . ', theme shop',
                'image'       => generateThemeUrl($theme->theme_code, $theme->section, $theme->theme_code),
                'url'         => url('/theme/' . $theme->id),
            ];
        });

        // หากไม่มีข้อมูล SEO ใน Cache ให้ส่ง HTTP 404
        if (!$seoData) {
            return response()->json(['message' => 'Theme not found'], 404);
        }

        return response()->json($seoData);
    }

    // ธีมอื่นๆค้นหาตามชื่อผู้สร้าง
    public function getThemeByAuthor(Request $request)
    {
        // ดึง 4 รายการที่ id น้อยกว่า $request->id
        $themesBefore = Theme::select('id', 'theme_code', 'title', 'country', 'price', 'section', 'created_at')
            ->where('author', $request->author)
            ->where('id', '<', $request->id)
            ->where('country', $request->country)
            ->where('status', 1)
            ->orderBy('id', 'desc') // เรียงลำดับจากมากไปน้อย
            ->take(4)
            ->get();

        // ดึง 4 รายการที่ id มากกว่า $request->id
        $themesAfter = Theme::select('id', 'theme_code', 'title', 'country', 'price', 'section', 'created_at')
            ->where('author', $request->author)
            ->where('id', '>', $request->id)
            ->where('country', $request->country)
            ->where('status', 1)
            ->orderBy('id', 'asc') // เรียงลำดับจากน้อยไปมาก
            ->take(4)
            ->get();

        // รวมผลลัพธ์
        $themes = $themesBefore->concat($themesAfter);

        // Map ข้อมูลเพื่อตอบกลับ
        $themeByAuthor = $themes->map(function ($theme) {
            $createdAt = Carbon::parse($theme->created_at);
            $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

            return [
                'id'         => $theme->id,
                'theme_code' => $theme->theme_code,
                'title'      => $theme->title,
                'country'    => $theme->country,
                'detail'     => $theme->detail ?? null,
                'price'      => convertLineCoin2Money($theme->price),
                'img_url'    => generateThemeUrl($theme->theme_code, $theme->section, $theme->theme_code),
                'created_at' => $theme->created_at->format('Y-m-d H:i:s'),
                'is_new'     => $isNew,
            ];
        });

        return response()->json($themeByAuthor);
    }

}
