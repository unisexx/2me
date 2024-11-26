<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function getThemeUpdate()
    {
        $themeUpdate = Theme::where('category', 'official')
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
                    'detail'     => $theme->detail,
                    'price'      => convertLineCoin2Money($theme->price),
                    'img_url'    => generateThemeUrl($theme->theme_code, $theme->section, $theme->theme_code),
                    'created_at' => $theme->created_at->format('Y-m-d H:i:s'),
                    'is_new'     => $isNew,
                ];
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
        $query = Theme::where('status', 1);

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
            return [
                'id'         => $theme->id,
                'theme_code' => $theme->theme_code,
                'title'      => $theme->title,
                'country'    => $theme->country,
                'price'      => convertLineCoin2Money($theme->price),
                'img_url'    => generateThemeUrl($theme->theme_code, $theme->section, $theme->theme_code),
                'created_at' => $theme->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json($themes);
    }

    public function getThemeView($id)
    {
        $theme = Theme::find($id);

        if (!$theme) {
            return response()->json(['message' => 'Theme not found'], 404);
        }

        $createdAt = Carbon::parse($theme->created_at);
        $isNew     = $createdAt->diffInDays(Carbon::now()) < 7;

        // แปลง $theme เป็น array และเพิ่มข้อมูลเพิ่มเติม
        $themeData = array_merge(
            $theme->toArray(),
            [
                'img_url' => generateThemeUrl($theme->theme_code, $theme->section, $theme->theme_code),
                'price'   => convertLineCoin2Money($theme->price),
                'is_new'  => $isNew,
            ]
        );

        // เก็บสถิติ views_last_3_days
        // recordProductView('theme', $theme->theme_code);

        return response()->json($themeData);
    }

    public function getThemeSEO($id)
    {
        $theme = Theme::find($id);

        if (!$theme) {
            return response()->json(['message' => 'Theme not found'], 404);
        }

        return response()->json([
            'title'       => $theme->title . ' - Line2Me Theme Shop',
            'description' => 'ซื้อธีมไลน์ ' . $theme->title . ' ในราคา ' . convertLineCoin2Money($theme->price) . ' บาท พร้อมส่งฟรี',
            'keywords'    => 'ธีมไลน์, ' . $theme->title . ', theme shop',
            'image'       => generateThemeUrl($theme->theme_code, $theme->section, $theme->theme_code),
            'url'         => url('/theme/' . $theme->id),
        ]);
    }

}
