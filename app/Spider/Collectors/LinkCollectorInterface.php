<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-08
 * Time: 02:23
 */

namespace App\Spider\Collectors;


use App\Sites\Simple\SiteInfo;
use App\Spider\CrawlUrl;
use App\Spider\Entities\Selector;
use Illuminate\Support\Collection;

interface LinkCollectorInterface {
    
    /**
     * Xử lý và lấy link con từ trang hiện tại
     *
     * @param CrawlUrl $crawlUrl
     *
     * @param Selector $selector
     *
     * @return mixed
     */
    public function getChildren(CrawlUrl $crawlUrl, Selector $selector) : Collection;
    
}