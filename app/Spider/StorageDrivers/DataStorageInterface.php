<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-08
 * Time: 03:59
 */

namespace App\Spider;


use App\Sites\Simple\SiteInfo;

interface DataStorageInterface {
    
    const SAVE_OVERWRITE = 'overwrite';
    const SAVE_NORMAL = 'normal';
    const SAVE_MERGE = 'merge';
    
    public function store( array $data, CrawlUrl $crawlUrl, SiteInfo $siteInfo = null ): bool;
    
    public function get( CrawlUrl $crawlUrl, SiteInfo $siteInfo = null ): array;
    
}