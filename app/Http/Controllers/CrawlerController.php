<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Emoji;
use App\Models\Series;
use App\Models\SeriesItem;
use App\Models\Sticker;
use App\Models\Theme;
use DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.crawler.index');
    }

    /**
     * ดึงสติ๊กเกอร์ไลน์จากเว็บ store.line ตาม sticker_code ที่กรอกข้อมูล
     */
    public function getsticker($sticker_code)
    {
        // นำ sticker_code มาค้นหาใน DB ว่ามีหรือไม่
        $rs = Sticker::select('id')->where('sticker_code', $sticker_code)->first();

        /**
         * ถ้ายังไม่มีใน DB
         */
        if (empty($rs->id)) {
            $client = new Client();
            $url    = 'https://store.line.me/stickershop/product/' . $sticker_code . '/th';

            // ส่ง HTTP Request ไปยัง URL
            $response = $client->request('GET', $url);
            $html     = $response->getBody()->getContents();

            // ใช้ DomCrawler ในการจัดการ HTML
            $crawler = new Crawler($html);

            /**
             * หา stamp_start, stamp_end และ version
             */
            $data    = [];
            $version = null;

            for ($i = 0; $i < 40; $i++) {
                if ($crawler->filter('div.mdCMN09LiInner.FnImage > span.mdCMN09Image:last-child')->eq($i)->count() != 0) {
                    $imgTxt     = $crawler->filter('div.mdCMN09LiInner.FnImage > span.mdCMN09Image:last-child')->eq($i)->attr('style');
                    $image_path = explode("/", $this->getUrlFromText($imgTxt));
                    $stamp_code = $image_path[6];
                    $version    = str_replace('v', '', $image_path[4]);

                    $data[] = [
                        'stamp_code' => $stamp_code,
                    ];
                }
            }

            /**
             * หา stamp json
             */
            $stamp = $crawler->filter('ul.FnStickerList > li.FnStickerPreviewItem')->each(function ($node) {
                $imgTxt     = $node->filter('div.mdCMN09LiInner.FnImage > span.mdCMN09Image:last-child')->attr('style');
                $image_path = explode("/", $this->getUrlFromText($imgTxt));
                $stamp_code = $image_path[6];
                return $stamp_code;
            });

            if (empty($version)) {
                return false;
            }

            /**
             * ดึงข้อมูลสติ๊กเกอร์จาก meta ไฟล์
             */
            $metaUrl      = 'http://dl.stickershop.line.naver.jp/products/0/0/' . $version . '/' . $sticker_code . '/LINEStorePC/productInfo.meta';
            $metaResponse = $client->request('GET', $metaUrl);
            $result       = $metaResponse->getBody()->getContents();
            $productInfo  = json_decode($result, true);

            /**
             * Insert ลงใน DB
             */
            DB::table('stickers')->insert(
                [
                    'sticker_code'        => @$sticker_code,
                    'version'             => @$version,
                    'title_th'            => @$productInfo['title']['th'] ?? $productInfo['title']['en'],
                    'title_en'            => @$productInfo['title']['en'],
                    'detail'              => @trim($crawler->filter('p.mdCMN38Item01Txt')->text()),
                    'author_th'           => @$productInfo['author']['th'] ?? $productInfo['author']['en'],
                    'author_en'           => @$productInfo['author']['en'],
                    'credit'              => @trim($crawler->filter('a.mdCMN38Item01Author')->text()),
                    'created_at'          => date("Y-m-d H:i:s"),
                    'category'            => @$sticker_code > 1000000 ? 'creator' : 'official',
                    'country'             => @$this->money2country(preg_replace('/[0-9.,\s]+/', '', $crawler->filter('p.mdCMN38Item01Price')->text())),
                    'price'               => @(int) $productInfo['price'][0]['price'],
                    'status'              => 1,
                    'onsale'              => @$productInfo['onSale'],
                    'validdays'           => @$productInfo['validDays'],
                    'hasanimation'        => @$productInfo['hasAnimation'],
                    'hassound'            => @$productInfo['hasSound'],
                    'stickerresourcetype' => @$productInfo['stickerResourceType'],
                    'stamp_start'         => @reset($data)['stamp_code'],
                    'stamp_end'           => @end($data)['stamp_code'],
                    'stamp'               => json_encode(@$stamp),
                ]
            );

            unset($data);
            dump($sticker_code);
        }

        // สั่งให้ปิด tab ถ้าจำเป็น
        if (@$_GET['closetab'] == 1) {
            echo "<script>window.close();</script>";
        }
    }

    /**
     * ดึงธีมไลน์จากเว็บ store.line ตาม theme_code ที่กรอกข้อมูล
     */
    public function gettheme($theme_code, $category = 'creator')
    {
        // นำ theme_code มาค้นหาใน DB ว่ามีหรือไม่
        $rs = Theme::where('theme_code', $theme_code)->first();

        // ถ้ายังไม่มีค่าใน DB
        if (empty($rs->section)) {
            $client = new Client();
            $url    = 'https://store.line.me/themeshop/product/' . $theme_code . '/th';

            // ส่ง HTTP Request
            $response = $client->request('GET', $url);
            $html     = $response->getBody()->getContents();

            // ใช้ DomCrawler จัดการ HTML
            $crawler = new Crawler($html);

            // ถ้า node ไม่ empty
            if ($crawler->filter('p.mdCMN38Item01Ttl')->count() > 0) {
                $imagePath = $crawler->filter('.mdCMN38Img img')->attr('src');

                // ใช้ Regular Expression ในการดึงเฉพาะเลข 234
                preg_match('/\/(\d+)\/WEBSTORE\/icon_198x278\.png$/', $imagePath, $matches);
                $imageNumber = $matches[1] ?? null;

                // บันทึกหรืออัปเดตข้อมูลใน DB
                Theme::updateOrCreate(
                    [
                        'theme_code' => $theme_code,
                    ],
                    [
                        'title'      => trim($crawler->filter('p.mdCMN38Item01Ttl')->text()),
                        'detail'     => trim($crawler->filter('p.mdCMN38Item01Txt')->text()),
                        'author'     => trim($crawler->filter('a.mdCMN38Item01Author')->text()),
                        'credit'     => trim($crawler->filter('p.mdCMN09Copy')->text()),
                        'created_at' => now(),
                        'category'   => $category,
                        'country'    => $this->money2country(preg_replace('/[0-9]+/', '', $crawler->filter('p.mdCMN38Item01Price')->text())),
                        'price'      => $this->getConvert2Coin(
                            (int) filter_var(trim($crawler->filter('p.mdCMN38Item01Price')->text()), FILTER_SANITIZE_NUMBER_INT),
                            $this->money2country(preg_replace('/[0-9]+/', '', $crawler->filter('p.mdCMN38Item01Price')->text()))
                        ),
                        'status'     => 1,
                        'section'    => $imageNumber,
                        'ok'         => 1,
                    ]
                );

                dump($theme_code);
            }
        }

        // สั่งให้ปิด tab ถ้าจำเป็น
        if (@$_GET['closetab'] == 1) {
            echo "<script>window.close();</script>";
        }
    }

    /**
     * ดึงอิโมจิไลน์จากเว็บ store.line ตาม emoji_code ที่กรอกข้อมูล
     */
    public function getemoji($emoji_code, $category = 'creator')
    {
        // นำ emoji_code มาค้นหาใน DB ว่ามีหรือไม่
        $rs = Emoji::select('id')->where('emoji_code', $emoji_code)->first();

        // ถ้ายังไม่มีค่าใน DB
        if (empty($rs->id)) {
            $client = new Client();
            $url    = 'https://store.line.me/emojishop/product/' . $emoji_code . '/th';

            // ส่ง HTTP Request
            $response = $client->request('GET', $url);
            $html     = $response->getBody()->getContents();

            // ใช้ DomCrawler จัดการ HTML
            $crawler = new Crawler($html);

            // insert ลง DB
            DB::table('emojis')->insert(
                [
                    'emoji_code'   => $emoji_code,
                    'title'        => trim($crawler->filter('.mdCMN38Item01Ttl')->text()),
                    'detail'       => trim($crawler->filter('.mdCMN38Item01Txt')->text()),
                    'creator_name' => trim($crawler->filter('.mdCMN38Item01Author')->text()),
                    'created_at'   => now(),
                    'category'     => $category,
                    'country'      => $this->money2country(
                        preg_replace('/[0-9]+/', '', $crawler->filter('p.mdCMN38Item01Price')->text())
                    ),
                    'price'        => $this->getConvert2Coin(
                        (int) filter_var(trim($crawler->filter('p.mdCMN38Item01Price')->text()), FILTER_SANITIZE_NUMBER_INT),
                        $this->money2country(
                            preg_replace('/[0-9]+/', '', $crawler->filter('p.mdCMN38Item01Price')->text())
                        )
                    ),
                    'status'       => 1,
                ]
            );

            dump($emoji_code);
        } // endif

        // สั่งให้ปิด tab ถ้าจำเป็น
        if (@$_GET['closetab'] == 1) {
            echo "<script>window.close();</script>";
        }
    }

    /**
     * ดึงสติ๊กเกอร์ไลน์จากเว็บ store.line
     * cat : top, new, top_creators, new_top_creators, new_creators
     * Page: หน้าที่จะเข้าไปดึงข้อมูล
     */
    public function getstickerstore($cat, $page = null)
    {
        $client     = new Client();
        $pageTarget = 'https://store.line.me/stickershop/showcase/' . $cat . '/th?page=' . $page;

        // ส่ง HTTP Request
        $response = $client->request('GET', $pageTarget);
        $html     = $response->getBody()->getContents();

        // ใช้ DomCrawler จัดการ HTML
        $crawler = new Crawler($html);

        // ดึงข้อมูลของสติ๊กเกอร์
        $crawler->filter('.mdCMN02Li')->each(function ($node) {
            // หา URL ของสติ๊กเกอร์
            $url = $node->filter('a')->attr('href');

            // เอาลิ้งค์สติ๊กเกอร์ที่ได้มา หาค่า sticker_code
            $sticker_code = explode("/", $url);
            $sticker_code = $sticker_code[3];

            // เรียกใช้งานฟังก์ชัน getsticker
            $this->getsticker($sticker_code);
        });

        // ดำเนินการเสร็จทั้งหมดแล้ว ให้ redirect ถ้า $page ยังไม่ถึงหน้าแรก
        if (isset($page) && $page != 1) {
            $page          = $page - 1;
            $page_redirect = url('admin/getstickerstore/' . $type . '/' . $cat . '/' . $page);
            echo "<script>setTimeout(function(){ window.location.href = '" . $page_redirect . "'; }, 1000);</script>";
        }
    }

    /**
     * ดึงธีมจากเว็บ store.line
     * Type: 1 = official, 2 = creator
     * cat : top, new, top_creators, new_creators
     * Page: หน้าที่จะเข้าไปดึงข้อมูล
     */
    public function getthemestore($type, $cat, $page = null)
    {
        $client     = new Client();
        $pageTarget = 'https://store.line.me/themeshop/showcase/' . $cat . '/th?page=' . $page;
        $category   = $type == 1 ? 'official' : 'creator';

        // ส่ง HTTP Request
        $response = $client->request('GET', $pageTarget);
        $html     = $response->getBody()->getContents();

        // ใช้ DomCrawler จัดการ HTML
        $crawler = new Crawler($html);

        // ดึงข้อมูลของธีม
        $crawler->filter('.mdCMN02Li')->each(function ($node) use ($category) {
            // หา URL ของธีม
            $url = $node->filter('a')->attr('href');

            // เอาลิงก์ธีมที่ได้มา หาค่า theme_code
            $theme_code = explode('/', $url);
            $theme_code = $theme_code[3];

            // เรียกใช้งานฟังก์ชัน gettheme
            $this->gettheme($theme_code, $category);
        });

        // ดำเนินการเสร็จทั้งหมดแล้ว ให้ redirect ถ้า $page ยังไม่ถึงหน้าแรก
        if (isset($page) && $page != 1) {
            $page          = $page - 1;
            $page_redirect = url('admin/getthemestore/' . $type . '/' . $cat . '/' . $page);
            echo "<script>setTimeout(function(){ window.location.href = '" . $page_redirect . "'; }, 1000);</script>";
        }
    }

    /**
     * ดึงอิโมจิจากเว็บ store.line
     * Type: 1 = official, 2 = creator
     * Cat : top, new, top_creators, new_top_creators, new_creators
     * Page: หน้าที่จะเข้าไปดึงข้อมูล
     */
    public function getemojistore($type, $cat, $page = null)
    {
        $client     = new Client();
        $pageTarget = 'https://store.line.me/emojishop/showcase/' . $cat . '/th?page=' . $page;
        $category   = $type == 1 ? 'official' : 'creator';

        // ส่ง HTTP Request
        $response = $client->request('GET', $pageTarget);
        $html     = $response->getBody()->getContents();

        // ใช้ DomCrawler จัดการ HTML
        $crawler = new Crawler($html);

        // ดึงข้อมูลของอิโมจิ
        $crawler->filter('.mdCMN02Li')->each(function ($node) use ($category) {
            // หา URL ของอิโมจิ
            $url = $node->filter('a')->attr('href');

            // เอาลิงก์อิโมจิที่ได้มา หาค่า emoji_code
            $emoji_code = explode("/", $url);
            $emoji_code = $emoji_code[3];

            // เรียกใช้งานฟังก์ชัน getemoji
            $this->getemoji($emoji_code, $category);
        });

        // ดำเนินการเสร็จทั้งหมดแล้ว ให้ redirect ถ้า $page ยังไม่ถึงหน้าแรก
        if (isset($page) && $page != 1) {
            $page          = $page - 1;
            $page_redirect = url('admin/getemojistore/' . $type . '/' . $cat . '/' . $page);
            echo "<script>setTimeout(function(){ window.location.href = '" . $page_redirect . "'; }, 1000);</script>";
        }
    }

    public function getRealLink($href)
    {
        /**
         * ค่าที่ได้จาก sticker :: /stickershop/product/1244010/th
         * ค่าที่ได้จาก theme :: https://store.line.me/themeshop/product/4c08fc1c-a1d2-4bd2-9bb7-d632962e09c2
         * ค่าที่ได้จาก emoji :: /emojishop/product/5cf2068b100cc3b7eeaa0f03/th
         */
        $type = explode("/", $href)[1];

        if ($type == 'stickershop' || $type == 'emojishop') {
            return 'https://store.line.me' . $href;
        } else {
            // theme
            return $href;
        }
    }

    public function getConvert2Bath($price, $country)
    {
        if ($country == 'th') {
            $Bath = [
                '30'  => '30',
                '60'  => '60',
                '90'  => '90',
                '120' => '120',
                '150' => '150',
            ];
        } elseif ($country == 'jp') {
            $Bath = [
                '120' => '30',
                '250' => '60',
                '370' => '90',
                '490' => '120',
                '610' => '150',
            ];
        } elseif ($country == 'tw') {
            $Bath = [
                '30'  => '30',
                '60'  => '60',
                '90'  => '90',
                '120' => '120',
                '150' => '150',
            ];
        }

        return @$Bath[$price];
    }

    public function getConvert2Coin($price, $country)
    {
        if ($country == 'th') {
            $Bath = [
                '31'  => '50',
                '35'  => '50',
                '65'  => '100',
                '69'  => '100',
                '99'  => '150',
                '120' => '200',
                '150' => '250',
            ];
        } elseif ($country == 'jp') {
            $Bath = [
                '120' => '50',
                '250' => '100',
                '370' => '150',
                '490' => '200',
                '610' => '250',
            ];
        } elseif ($country == 'tw') {
            $Bath = [
                '30'  => '50',
                '60'  => '100',
                '90'  => '150',
                '120' => '200',
                '150' => '250',
            ];
        }

        return @$Bath[$price];
    }

    public function getStickerByAuthor($authorID, $page = null)
    {
        $client = new Client();

        // ตรวจสอบว่า $authorID มีอยู่ในตาราง author_log แล้วหรือไม่
        $existingAuthor = DB::table('author_log')->where('author_id', $authorID)->where('type', 'sticker')->first();
        if (!$existingAuthor) {
            DB::table('author_log')->insert([
                'author_id'  => $authorID,
                'type'       => 'sticker',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // หน้าเพจเป้าหมาย
        $pageTarget = 'https://store.line.me/stickershop/author/' . $authorID . '?page=' . $page;

        // ส่ง HTTP Request
        $response = $client->request('GET', $pageTarget);
        $html     = $response->getBody()->getContents();

        // ใช้ DomCrawler จัดการ HTML
        $crawler = new Crawler($html);

        // ดึงข้อมูล sticker_code
        $stickerCodeArray = $crawler->filter('.mdCMN02Li')->each(function ($node) {
            $url          = $node->filter('a')->attr('href');
            $sticker_code = explode("/", $url)[3];
            return $sticker_code;
        });

        // ดึงข้อมูลสติ๊กเกอร์
        $this->getStickerArray($stickerCodeArray);

        // Redirect หรือปิดแท็บ
        if (isset($page) && $page != 1) {
            $page          = $page - 1;
            $page_redirect = url('admin/get-sticker-by-author/' . $authorID . '/' . $page);
            echo "<script>
            setTimeout(function(){
                window.location.href = '" . $page_redirect . "';
            }, 1000);
        </script>";
        } else {
            echo "<script>
            setTimeout(function(){
                window.close();
            }, 1000);
        </script>";
        }
    }

    public function getThemeByAuthor($authorID, $page = null)
    {
        $client = new Client();

        // ตรวจสอบว่า $authorID มีอยู่ในตาราง author_log แล้วหรือไม่
        $existingAuthor = DB::table('author_log')->where('author_id', $authorID)->where('type', 'theme')->first();
        if (!$existingAuthor) {
            DB::table('author_log')->insert([
                'author_id'  => $authorID,
                'type'       => 'theme',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // หน้าเพจเป้าหมาย
        $pageTarget = 'https://store.line.me/themeshop/author/' . $authorID . '?page=' . $page;

        // ส่ง HTTP Request
        $response = $client->request('GET', $pageTarget);
        $html     = $response->getBody()->getContents();

        // ใช้ DomCrawler จัดการ HTML
        $crawler = new Crawler($html);

        // ดึงข้อมูล theme_code
        $themeCodeArray = $crawler->filter('.mdCMN02Li')->each(function ($node) {
            $url        = $node->filter('a')->attr('href');
            $theme_code = explode('/', $url)[3];
            return $theme_code;
        });

        // ดึงข้อมูลธีม
        $this->getThemeArray($themeCodeArray);

        // Redirect หรือปิดแท็บ
        if (isset($page) && $page != 1) {
            $page          = $page - 1;
            $page_redirect = url('admin/get-theme-by-author/' . $authorID . '/' . $page);
            echo "<script>
            setTimeout(function(){
                window.location.href = '" . $page_redirect . "';
            }, 1000);
        </script>";
        } else {
            echo "<script>
            setTimeout(function(){
                window.close();
            }, 1000);
        </script>";
        }
    }

    public function getThemeArray($themeCodeArray, $category = 'creator')
    {
        foreach ($themeCodeArray as $theme_code) {
            $this->gettheme($theme_code, $category = 'creator');
        }
    }

    public function getStickerArray($stickerCodeArray)
    {
        $client = new Client();
        $rs     = Sticker::select('sticker_code')->whereIn('sticker_code', $stickerCodeArray)->pluck('sticker_code')->toArray();
        $rs2    = Emoji::select('emoji_code')->whereIn('emoji_code', $stickerCodeArray)->pluck('emoji_code')->toArray();

        $differenceArray  = array_diff($stickerCodeArray, $rs);
        $differenceArray2 = array_diff($differenceArray, $rs2);
        dump($differenceArray2);

        $arrayDataSticker = [];
        $arrayDataEmoji   = [];

        foreach ($differenceArray2 as $sticker_code) {
            if (Str::length($sticker_code) != 24) {
                // ส่ง HTTP Request
                $response = $client->request('GET', 'https://store.line.me/stickershop/product/' . $sticker_code . '/th');
                $html     = $response->getBody()->getContents();

                // ใช้ DomCrawler จัดการ HTML
                $crawler = new Crawler($html);

                $data    = [];
                $version = null;

                /**
                 * หา stamp_start & stamp_end & version
                 */
                for ($i = 0; $i < 40; $i++) {
                    if ($crawler->filter('div.mdCMN09LiInner.FnImage > span.mdCMN09Image:last-child')->eq($i)->count() != 0) {
                        $imgTxt     = $crawler->filter('div.mdCMN09LiInner.FnImage > span.mdCMN09Image:last-child')->eq($i)->attr('style');
                        $image_path = explode("/", getUrlFromText($imgTxt));
                        $stamp_code = $image_path[6];
                        $version    = str_replace('v', '', $image_path[4]);

                        $data[] = [
                            'stamp_code' => $stamp_code,
                        ];
                    }
                }

                /**
                 * หา stamp json
                 */
                $stamp = $crawler->filter('ul.FnStickerList > li.FnStickerPreviewItem')->each(function ($node) {
                    $imgTxt     = $node->filter('div.mdCMN09LiInner.FnImage > span.mdCMN09Image:last-child')->attr('style');
                    $image_path = explode("/", getUrlFromText($imgTxt));
                    $stamp_code = $image_path[6];
                    return $stamp_code;
                });

                if (empty($version)) {
                    continue;
                }

                /**
                 * ดึงข้อมูลสติ๊กเกอร์จาก meta ไฟล์
                 */
                $metaUrl      = 'http://dl.stickershop.line.naver.jp/products/0/0/' . $version . '/' . $sticker_code . '/LINEStorePC/productInfo.meta';
                $metaResponse = $client->request('GET', $metaUrl, ['verify' => false]);
                $metaData     = json_decode($metaResponse->getBody()->getContents(), true);

                /**
                 * เพิ่มข้อมูลลง array
                 */
                $arrayDataSticker[] = [
                    'sticker_code'        => $sticker_code,
                    'version'             => $version,
                    'title_th'            => $metaData['title']['th'] ?? $metaData['title']['en'],
                    'title_en'            => $metaData['title']['en'],
                    'detail'              => trim($crawler->filter('p.mdCMN38Item01Txt')->text() ?? ''),
                    'author_th'           => $metaData['author']['th'] ?? $metaData['author']['en'],
                    'author_en'           => $metaData['author']['en'],
                    'credit'              => trim($crawler->filter('a.mdCMN38Item01Author')->text() ?? ''),
                    'created_at'          => now(),
                    'category'            => $sticker_code > 1000000 ? 'creator' : 'official',
                    'country'             => $this->money2country(preg_replace('/[0-9]+/', '', $crawler->filter('p.mdCMN38Item01Price')->text() ?? '')),
                    'price'               => (int) $metaData['price'][0]['price'],
                    'status'              => 1,
                    'onsale'              => $metaData['onSale'],
                    'validdays'           => $metaData['validDays'],
                    'hasanimation'        => $metaData['hasAnimation'],
                    'hassound'            => $metaData['hasSound'],
                    'stickerresourcetype' => $metaData['stickerResourceType'],
                    'stamp_start'         => reset($data)['stamp_code'] ?? null,
                    'stamp_end'           => end($data)['stamp_code'] ?? null,
                    'stamp'               => json_encode($stamp),
                ];

                dump($sticker_code);
            } else {
                // กรณีเป็น Emoji
                $response = $client->request('GET', 'https://store.line.me/emojishop/product/' . $sticker_code . '/th');
                $html     = $response->getBody()->getContents();

                $crawler = new Crawler($html);

                $arrayDataEmoji[] = [
                    'emoji_code'   => $sticker_code,
                    'title'        => trim($crawler->filter('.mdCMN38Item01Ttl')->text() ?? ''),
                    'detail'       => trim($crawler->filter('.mdCMN38Item01Txt')->text() ?? ''),
                    'creator_name' => trim($crawler->filter('.mdCMN38Item01Author')->text() ?? ''),
                    'created_at'   => now(),
                    'category'     => 'creator',
                    'country'      => $this->money2country(preg_replace('/[0-9]+/', '', $crawler->filter('p.mdCMN38Item01Price')->text() ?? '')),
                    'price'        => $this->getConvert2Coin(
                        (int) filter_var(trim($crawler->filter('p.mdCMN38Item01Price')->text()), FILTER_SANITIZE_NUMBER_INT),
                        $this->money2country(preg_replace('/[0-9]+/', '', $crawler->filter('p.mdCMN38Item01Price')->text()))
                    ),
                    'status'       => 1,
                ];

                dump($sticker_code);
            }
        }

        // บันทึกข้อมูลลงฐานข้อมูล
        if (!empty($arrayDataSticker)) {
            Sticker::insert($arrayDataSticker);
        }

        if (!empty($arrayDataEmoji)) {
            Emoji::insert($arrayDataEmoji);
        }
    }

    function getUrlFromText($text)
    {
        // $text = 'width: 122px; height: 140px; background-image:url(https://stickershop.line-scdn.net/stickershop/v1/sticker/2428/android/sticker.png;compress=true); background-size: 122px 140px;';
        preg_match('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $text, $matches);
        // print_r($matches);

        return $matches[0];
    }

    function money2country($currency)
    {
        $country = ['￥' => 'jp', 'THB' => 'th', 'NT$' => 'tw', 'Rp' => 'id', '$' => 'us'];

        return $country[$currency];
    }
}
