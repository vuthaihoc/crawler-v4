<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-11
 * Time: 15:09
 */

namespace App\Spider\Collectors;


use App\Sites\Simple\SiteInfo;
use App\Spider\Browsers\BrowserInterface;
use App\Spider\CrawlUrl;
use App\Spider\Entities\Link;
use App\Spider\Entities\Selector;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

class RuledLinkCollector implements LinkCollectorInterface {
    
    use CrawlerHelper;
    
    /**
     * Xử lý và lấy link con từ trang hiện tại
     *
     * @param CrawlUrl $crawlUrl
     *
     * @param Selector $selector
     *
     * @return mixed
     */
    public function getChildren( CrawlUrl $crawlUrl, Selector $selector ): Collection {
        $children = new Collection();
        if ( ! $crawlUrl->getHtml() ) {
            return $children;
        }
        try {
            $elms = $this->getElements( $crawlUrl->getHtml(), $selector );
            if ( $elms->nodeName() != "a" ) {
                $elms = $this->getElements( $elms, new Selector( "css", "a" ) );
            }
            foreach ( $elms as $elm ) {
                if ( $link = $this->getDomNodeLinkInfo( $elm ) ) {
                    $link->href = CrawlUrl::join( $crawlUrl->getUrl(), $link->href );
                    $children->add( $link );
                }
            }
        } catch ( \Exception $ex ) {
        
        }
        if($selector->multiple == false && $children->count() > 1){
            return $children->slice( 0, 1);
        }else{
            return $children;
        }
    }
    
}