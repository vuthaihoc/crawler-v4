<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-08
 * Time: 04:07
 */

namespace App\Spider;


class Monitor {
    
    
    /**
     * Monitor constructor.
     *
     * @param LinkCollectorInterface $link_collector
     * @param DataCollectorInterface $data_collector
     */
    public function __construct(LinkCollectorInterface $link_collector, DataCollectorInterface $data_collector) {
    
    }
    
    public function linksOfSite($url, $filter = [], $limit = 100, $offset = 0){
    
    }
    
    public function totalLinksOfSite($url){
    
    }
    
    public function linkInfo($url, $include_data = false){
    
    }
    
}