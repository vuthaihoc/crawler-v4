<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-21
 * Time: 01:47
 */

namespace App\Spider\Collectors;


use App\Sites\Simple\SiteInfo;
use App\Spider\CrawlUrl;
use App\Spider\Entities\DataRule;

class RuledDataCollector implements DataCollectorInterface {
    
    use CrawlerHelper;
    
    public function getData( CrawlUrl $crawlUrl, SiteInfo $siteInfo ): array {
        $data = [];
        $page = $siteInfo->getPage( $crawlUrl->getStep() );
        if ( $page ) {
            $rules = $page->data_rules;
            /** @var DataRule $rule */
            foreach ( $rules as $rule ) {
                $data[ $rule->name ] = $this->getPageData( $crawlUrl, $rule );
            }
        }
        return $data;
    }
    
    protected function getPageData( CrawlUrl $crawlUrl, DataRule $rule ) {
        $element = $this->getElements( $crawlUrl->getHtml(), $rule->selector );
        if ( $element ) {
            switch ( $rule->type ) {
                case DataRule::TYPE_TEXT:
                    return $element->text();
                    break;
                case DataRule::TYPE_HTML:
                    return $element->html();
                    break;
                case DataRule::TYPE_URL:
                    if($element->nodeName() == 'a'){
                        return $element->attr('href');
                    }elseif($element->nodeName() == 'img'){
                        return $element->attr('src');
                    }
                    break;
                case DataRule::TYPE_ATTRIBUTE:
                    return $element->attr($rule->what);
                    break;
            }
        }
        return null;
    }
    
}