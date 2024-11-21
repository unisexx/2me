<?php

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
