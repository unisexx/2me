<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteOldProductViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product-views:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete product views older than 3 days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // SQL Query to delete records older than 3 days
        $deletedRows = DB::table('product_views')
            ->where('created_at', '<', now()->subDays(3))
            ->delete();

        // Log the result
        $this->info("Deleted {$deletedRows} old product views.");
        return 0;
    }
}
