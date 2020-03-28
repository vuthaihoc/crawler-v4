<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-16
 * Time: 06:01
 */

namespace App\Spider;


use App\Sites\Simple\SiteInfo;
use App\Spider\Browsers\BrowserInterface;
use App\Spider\Browsers\BrowserManager;
use App\Spider\Collectors\DataCollectorInterface;
use App\Spider\Collectors\LinkCollectorInterface;
use App\Spider\Collectors\RuledDataCollector;
use App\Spider\Collectors\RuledLinkCollector;
use App\Spider\Entities\NavigationRule;
use App\Spider\Entities\Page;
use App\Spider\Enum\ProcessStatus;
use App\Spider\Enum\ProcessType;
use App\Spider\Exceptions\SitePageNotFound;
use App\Spider\QueueDrivers\CrawlQueueInterface;
use Symfony\Component\DomCrawler\Crawler;

class SpiderRunner {
    
    protected $site;
    
    /** @var LinkCollectorInterface */
    protected $link_collector;
    /** @var DataCollectorInterface */
    protected $data_collector;
    /** @var DataStorageInterface */
    protected $data_storage;
    protected $queue;
    /** @var BrowserInterface */
    protected $browser;
    /** @var BrowserInterface */
    protected $js_browser;
    protected $last_render_time;
    
    /**
     * SpiderRunner constructor.
     *
     * @param SiteInfo $site
     * @param DataStorageInterface $data_storage
     * @param CrawlQueueInterface $queue
     *
     * @throws \Exception
     */
    public function __construct(
        SiteInfo $site,
        DataStorageInterface $data_storage,
        CrawlQueueInterface $queue = null
    ) {
        $this->site = $site;
        $this->queue = $queue;
        $this->data_storage = $data_storage;
        
        $this->link_collector = new RuledLinkCollector();
        $this->data_collector = new RuledDataCollector();
        
        
        $this->browser = BrowserManager::get( 'guzzle');
        $this->js_browser = BrowserManager::get( 'phantomjs');
    }
    
    public function crawl_init(){
        // validate
        
        // add start page
        /** @var Page $page */
        foreach ($this->site->getPages() as $page){
            if(!$page->is_root){
                continue;
            }
            $crawl_url = CrawlUrl::create( $page->url, $page->name, '', null, $page->js );
            $this->queue->add( $crawl_url );
        }
    }
    
    public function run_crawl_links($get_data = false){
        do{
            $pending_link_url = $this->queue->getFirstPendingUrl(ProcessType::LINK);
            if($pending_link_url){
                $this->log( $pending_link_url );
                $this->getChildren($pending_link_url);
                $this->queue->changeProcessStatus( $pending_link_url, ProcessType::LINK, ProcessStatus::SUCCESS );
            }
            if($get_data){
                $pending_data_url = $this->queue->getFirstPendingUrl(ProcessType::DATA);
                if($pending_data_url){
                    $this->log( $pending_data_url, "Get Data");
                    $this->renderHtml( $pending_data_url );
                    $data = $this->data_collector->getData( $pending_data_url, $this->site );
                    if(count( $data )){
                        $this->data_storage->store( $data, $pending_data_url, $this->site );
                    }
                    $this->queue->changeProcessStatus( $pending_data_url, ProcessType::DATA, ProcessStatus::SUCCESS );
                }
            }else{
                $pending_data_url = false;
            }
        }while($pending_link_url || $pending_data_url);
        
    }
    
    public function run_get_data($sleep = 5){
        do{
            $pending_data_url = $this->queue->getFirstPendingUrl(ProcessType::DATA);
            if($pending_data_url){
                $data = $this->data_collector->getData( $pending_data_url, $this->site );
                if(count( $data )){
                    $this->data_storage->store( $data, $pending_data_url, $this->site );
                }
                $this->queue->changeProcessStatus( $pending_data_url, ProcessType::DATA, ProcessStatus::SUCCESS );
            }else{
                sleep($sleep);
            }
        }while($pending_data_url);
    }
    
    /**
     * Kiểm tra xem site đã lấy hết link/data chưa
     *
     * @param string $type
     *
     * @return bool
     */
    protected function isDone($type = 'all') : bool {
    
    }
    
    protected function getChildren(CrawlUrl $crawlUrl){
        $this->renderHtml( $crawlUrl );
        $page = $this->site->getPage($crawlUrl->getStep());
        if(!$page){
            throw new SitePageNotFound("Can not find site page config for " . $crawlUrl->getStep());
        }
        
        $this->log( $crawlUrl, "Get Child");
        
        $navigation_rules = $page->getNavigationRules();
        
        start_get_children_by_rules:
        
        /**
         * @var string $page_name
         * @var NavigationRule $rule
         */
        foreach ($navigation_rules as $page_name => $rule){
            if($rule->type == "link"){
                $children_urls = $this->link_collector->getChildren( $crawlUrl, $rule->selector );
                foreach ($children_urls as $link){
                    $this->queue->add( CrawlUrl::create( $link->href, $page_name, '', $crawlUrl, $this->site->getPage( $page_name )->js) );
                }
            }
        }
    }
    
    protected function renderHtml(CrawlUrl $crawlUrl){
        $this->sleepBeforeRender();
        if($crawlUrl->jsRender()){
            $html = $this->js_browser->getHtml( $crawlUrl->getUrl() );
        }else{
            $html = $this->browser->getHtml( $crawlUrl->getUrl() );
        }
        $crawler = new Crawler();
        $crawler->addHtmlContent( $html );
        $crawlUrl->setHtml( $crawler );
    }
    
    protected function sleepBeforeRender(){
        if($this->site->getDelay() == 0){
            return;
        }
        $now = now()->timestamp;
        $sleep_time = $this->site->getDelay() - ( $now - $this->last_render_time );
        if($sleep_time > 0){
            sleep( $sleep_time );
        }
        $this->last_render_time = now()->timestamp;
    }
    
    protected function log(CrawlUrl $crawlUrl, $prefix = '', $data = null){
        if(!$prefix){
            dump("[" . $crawlUrl->getStep() . "]"
                     . ($crawlUrl->jsRender() ? "[JS]" : "")
                     . $crawlUrl->getUrl() . " - "
                     . $crawlUrl->getUrlHash() . " - "
                     . $crawlUrl->getParentUrlHash() );
        }else{
            dump( $prefix );
        }
        if(!empty( $data )){
            dump($data);
        }
    }
    
}