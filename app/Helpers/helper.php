<?php

use App\Models\Emoji;
use App\Models\Sticker;
use App\Models\Theme;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

if (!function_exists('getStickerImgUrl')) {
    function getStickerImgUrl($stickerresourcetype, $version, $sticker_code)
    {
        if ($stickerresourcetype == 'ANIMATION' || $stickerresourcetype == 'ANIMATION_SOUND') {
            $imgUrl = 'https://stickershop.line-scdn.net/stickershop/v' . $version . '/product/' . $sticker_code . '/IOS/main_animation@2x.png';
        } elseif ($stickerresourcetype == 'POPUP' || $stickerresourcetype == 'POPUP_SOUND') {
            $imgUrl = 'https://sdl-stickershop.line.naver.jp/stickershop/v' . $version . '/product/' . $sticker_code . '/IOS/main_popup.png';
        } else {
            $imgUrl = 'https://sdl-stickershop.line.naver.jp/products/0/0/' . $version . '/' . $sticker_code . '/LINEStorePC/main.png';
        }

        return $imgUrl;
    }
}

if (!function_exists('convertLineCoin2Money')) {
    function convertLineCoin2Money($coin)
    {
        $bath = ['250' => '170', '200' => '130', '150' => '95', '100' => '65', '85' => '55', '50' => '35', '10' => '6', '2' => '1'];

        return @$bath[$coin];
    }
}

if (!function_exists('generateThemeUrl')) {
    function generateThemeUrl($uuid, $section = false)
    {
        $section      = !empty($section) ? $section : 1;
        $baseUrl      = 'https://shop.line-scdn.net/themeshop/v1/products/';
        $formattedUrl = $baseUrl . substr($uuid, 0, 2) . '/' . substr($uuid, 2, 2) . '/' . substr($uuid, 4, 2) . '/' . $uuid . '/' . $section . '/WEBSTORE/icon_198x278.png';

        return $formattedUrl;
    }
}

if (!function_exists('generateThemeUrlDetail')) {
    function generateThemeUrlDetail($uuid, $imgOrder, $section = false)
    {
        $section      = !empty($section) ? $section : 1;
        $baseUrl      = 'https://shop.line-scdn.net/themeshop/v1/products/';
        $formattedUrl = $baseUrl . substr($uuid, 0, 2) . '/' . substr($uuid, 2, 2) . '/' . substr($uuid, 4, 2) . '/' . $uuid . '/' . $section . '/ANDROID/th/preview_00' . $imgOrder . '_720x1232.png';

        return $formattedUrl;
    }
}

// อัพเดท views_last_3_days
if (!function_exists('recordProductView')) {
    function recordProductView($type, $id, $clientIp = null)
    {
        // ตรวจสอบ User-Agent ว่าเป็น Bot หรือไม่
        $userAgent = request()->header('User-Agent');
        if (isBot($userAgent)) {
            return; // ถ้าเป็น Bot ให้หยุดทำงาน
        }

        // ใช้ IP ที่รับจาก API หากมี ไม่เช่นนั้นใช้ IP จากคำขอปัจจุบัน
        $ipAddress = $clientIp ?? (request()->header('X-Forwarded-For')
                ? explode(',', request()->header('X-Forwarded-For'))[0]
                : request()->ip());

        $today = Carbon::today();

        // ใช้ insertOrIgnore เพื่อบันทึกข้อมูลโดยป้องกันการบันทึกซ้ำ
        DB::table('product_views')->insertOrIgnore([
            'product_id' => $id,
            'type'       => $type,
            'ip_address' => $ipAddress,
            'view_date'  => $today,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // นับยอดวิวในช่วง 3 วันล่าสุด
        $threeDaysAgo = Carbon::now()->subDays(3);
        $viewsCount   = DB::table('product_views')
            ->where('product_id', $id)
            ->where('type', $type)
            ->where('view_date', '>=', $threeDaysAgo)
            ->count();

        // อัพเดทยอดวิวในตารางที่เกี่ยวข้อง
        switch ($type) {
            case 'sticker':
                Sticker::where('sticker_code', $id)->first()->update(['views_last_3_days' => $viewsCount]);
                break;
            case 'theme':
                Theme::find($id)->update(['views_last_3_days' => $viewsCount]);
                break;
            case 'emoji':
                Emoji::find($id)->update(['views_last_3_days' => $viewsCount]);
                break;
        }
    }
}

if (!function_exists('isBot')) {
    /**
     * ตรวจสอบว่า User-Agent เป็น Bot หรือไม่
     */
    function isBot($userAgent)
    {
        $botKeywords = [
            'bot', 'crawl', 'spider', 'slurp', 'search', 'yandex', 'baidu', 'bing', 'duckduckgo', 'google', 'mediapartners', 'facebookexternalhit', 'linkedinbot', 'twitterbot', 'facebot',
        ];

        if (!$userAgent) {
            return false; // หากไม่มี User-Agent ให้ถือว่าไม่ใช่ Bot
        }

        foreach ($botKeywords as $botKeyword) {
            if (stripos($userAgent, $botKeyword) !== false) {
                return true; // ถ้าพบคำที่ระบุว่าเป็น Bot ใน User-Agent
            }
        }

        return false; // ไม่พบว่าเป็น Bot
    }
}

if (!function_exists('storeViewHistory')) {
    function storeViewHistory($type, $id)
    {
        $sessionId = Session::getId();
        $key       = "session:{$sessionId}:viewed_{$type}s";

                                   // เก็บ product_id ใน Redis โดยใช้ list
        Redis::lrem($key, 0, $id); // ลบรายการที่ซ้ำกัน
        Redis::lpush($key, $id);   // เพิ่ม product_id เข้าไปที่หัวรายการ

        // เก็บประวัติสูงสุด 12 รายการ
        Redis::ltrim($key, 0, 11);
    }
}
