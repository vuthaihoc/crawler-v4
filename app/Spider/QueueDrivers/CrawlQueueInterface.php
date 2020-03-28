<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-08
 * Time: 02:27
 */

namespace App\Spider\QueueDrivers;


use App\Spider\CrawlUrl;
use App\Spider\Enum\ProcessStatus;
use App\Spider\Enum\ProcessType;

interface CrawlQueueInterface {
    
    public function add(CrawlUrl $url);
    
    public function has(CrawlUrl $crawlUrl): bool;
    
    public function getUrlById($id): CrawlUrl;
    
    public function hasPendingUrls(string $type): bool;
    
    /**
     * Get pending url and mark as processing
     *
     * @param string $type
     *
     * @see ProcessType
     *
     * @return mixed
     */
    public function getFirstPendingUrl(string $type) : ?CrawlUrl;
    
    /**
     *
     *
     * @param CrawlUrl $url
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasAlreadyBeenProcessed(CrawlUrl $url, string $type): bool;
    
    /**
     * @param CrawlUrl $crawlUrl
     * @param string $type
     * @param $status
     *
     * @return mixed
     * @see ProcessStatus
     */
    public function changeProcessStatus(CrawlUrl $crawlUrl, string $type, $status);
    
}