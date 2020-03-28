<?php

namespace App\Console\Commands\Crawler;

use App\Sites\Simple\SiteInfo;
use App\Spider\Enum\ProcessStatus;
use App\Spider\QueueDrivers\SqliteQueue;
use Illuminate\Console\Command;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\Table;

class MonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor crawling status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $db_file = storage_path("itviec.com.sqlite");
        $queue = new SqliteQueue($db_file, 'itviec.com');
        get_data:
        $this->info( "Getting Children:");
        /** @var Paginator $running_pages */
        $running_pages = $queue->paginate('link', ProcessStatus::PROCESSING, 1, 5);
        $this->table( [ "visited", "url" ], $running_pages->map(function ($item, $key) {
            return [$item->visited, $item->url];
        })->toArray());
        $this->info( "Getting Data:");
        /** @var Paginator $running_pages */
        $running_pages = $queue->paginate('data', ProcessStatus::PROCESSING, 1, 5);
        $this->table( [ "visited", "url" ], $running_pages->map(function ($item, $key) {
            return [$item->visited, $item->url];
        })->toArray());
        sleep( 1);
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            system('cls');
        } else {
            system('clear');
        }
        goto get_data;
    }
}
