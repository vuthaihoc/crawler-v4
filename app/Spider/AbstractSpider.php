<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-08
 * Time: 03:56
 */

namespace App\Spider;


abstract class AbstractSpider {
    
    protected $home;// home page url
    
    /** @var CrawlQueueInterface */
    protected $queue;
    /** @var LinkCollectorInterface */
    protected $link_collector;
    /** @var DataCollectorInterface */
    protected $data_collector;
    
    protected $max_url = -1;
    
    /**
     * AbstractSpider constructor.
     *
     * @param $start_urls
     * @param $link_collector
     * @param $data_collector
     * @param string $home
     * @param null $queue
     */
    public function __construct( $start_urls, $link_collector, $data_collector, $home = '', $queue = null) {
        $this->home = $home;
    }
    
    public function setQueue(CrawlQueueInterface $queue){
        $this->queue = $queue;
        return $this;
    }
    public function getQueue(){
        return $this->queue;
    }
    
    public function setLinkCollector(LinkCollectorInterface $linkCollector){
        $this->link_collector = $linkCollector;
        return $this;
    }
    public function getLinkCollector(){
        return $this->link_collector;
    }
    
    public function setDataCollector(DataCollectorInterface $dataCollector){
        $this->data_collector = $dataCollector;
        return $this;
    }
    public function getDataCollector(){
        return $this->data_collector;
    }
    
    /**
     * @param bool $get_data get data after get link
     */
    public function runLinkCollector($get_data = false){
    
    }
    
    /**
     * Get data
     */
    public function runDataCollector(){
    
    }
    
}