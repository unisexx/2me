<?php

namespace App\Http\Controllers;

use App\Models\Promote;
use App\Models\Sticker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PromoteController extends Controller
{
    public function getPromoteSticker()
    {
        // ใช้ Cache เป็นเวลา 1 วัน (1440 นาที)
        $stickerPromote = Cache::remember('promote_sticker', 1440, function () {
            $productCodeArray = Promote::where('product_type', 'sticker')
                ->where('end_date', '>=', Carbon::now()->toDateString())
                ->orderBy('id', 'desc')
                ->pluck('product_code')
                ->toArray();

            if (empty($productCodeArray)) {
                return collect();
            }

            // ดึงข้อมูล sticker โดยใช้ product_code ที่ได้มา และจัดเรียงตามลำดับของ productCodeArray
            $stickerCodes = implode(',', array_map(function ($code) {
                return "'" . addslashes($code) . "'"; // เพิ่ม addslashes เพื่อหลีกเลี่ยงปัญหา escape characters
            }, $productCodeArray));

            return Sticker::select('sticker_code', 'title_th', 'country', 'price', 'stickerresourcetype', 'version', 'created_at')
                ->whereIn('sticker_code', $productCodeArray)
                ->orderByRaw("FIELD(sticker_code, $stickerCodes)")
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
        });

        return response()->json($stickerPromote);
    }
}
