<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-08
 * Time: 02:50
 */

namespace App\Spider\Collectors;


use App\Sites\Simple\SiteInfo;
use App\Spider\CrawlUrl;

interface DataCollectorInterface {
    
    public function getData( CrawlUrl $crawlUrl, SiteInfo $siteInfo ): array;
    
}