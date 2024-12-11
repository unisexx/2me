<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RunCrawlerTopCreator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:RunCrawlerTopCreator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ดึงข้อมูลสติกเกอร์ top creators';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Replace these values as necessary
        $category = 'top_creators';
        $page     = 1;

        $url = url("/getstickerstore/{$category}/{$page}");

        // Use HTTP request to call the URL
        $response = Http::get($url);

        if ($response->ok()) {
            $this->info('Crawler executed successfully.');
        } else {
            $this->error('Failed to execute crawler.');
        }

        return 0;
    }
}
