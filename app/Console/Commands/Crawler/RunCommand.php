<?php

namespace App\Console\Commands\Crawler;

use App\Sites\Simple\SiteInfo;
use App\Spider\QueueDrivers\SqliteQueue;
use App\Spider\SpiderRunner;
use App\Spider\StorageDrivers\FileStorage;
use Illuminate\Console\Command;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class RunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:run
    {--site=: Site will be crawl}
    {--mode=all : Run mode all/link/data}
    {--reset : Reset queue before run}
    {--resume=0: Resume queue before run, if value x > 1, all processing task before x minutes from now will be reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run crawl a site';

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
        $site = new SiteInfo( 'itviec.com');
        $db_file = storage_path("itviec.com.sqlite");
        $queue = new SqliteQueue($db_file, 'itviec.com');
        $data_storage = new FileStorage(
            new Filesystem(
                new Local(
                    storage_path("data/itviec.com/")
                )
            )
        );
        
        $mode = $this->option("mode");
        
        if($this->option('reset')){
            $queue->reset();
        }
        
        $resume = $this->option('resume');
        switch ($resume){
            case 0:
                break;
            case 1:
                if($mode == 'all'){
                    $queue->resume( 'link');
                    $queue->resume( 'data');
                }elseif ($mode){
                    $queue->resume( $mode);
                }
                break;
            default:
                if($mode == 'all'){
                    $queue->resume( 'link', (int)$resume);
                    $queue->resume( 'data', (int)$resume);
                }elseif ($mode){
                    $queue->resume( $mode, (int)$resume);
                }
        }
        
        $spider = new SpiderRunner( $site, $data_storage, $queue );
        $spider->crawl_init();
        
        switch ($mode){
            case "all":
                $spider->run_crawl_links(true);
                break;
            case "link":
                $spider->run_crawl_links();
                break;
            case "data":
                $spider->run_get_data();
                break;
        }
//        dd($queue->paginate());
    }
}
