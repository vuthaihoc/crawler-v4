<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-25
 * Time: 04:41
 */

namespace App\Spider\Collectors;


use App\Spider\Entities\Link;
use App\Spider\Entities\Selector;
use Symfony\Component\DomCrawler\Crawler;

trait CrawlerHelper {
    protected function getElements( Crawler $crawler, Selector $selector ): ?Crawler {
        try {
            if ( $selector->type == 'css' ) {
                $elm = $crawler->filter( $selector->content );
            } elseif ( $selector->type == 'xpath' ) {
                $elm = $crawler->filterXPath( $selector->content );
            }
            
            return $elm;
        } catch ( \Exception $ex ) {
        
        }
        
        return null;
    }
    
    protected function getDomNodeLinkInfo( \DOMNode $a ): ?Link {
        
        $link = new Link();
        
        try {
            if ( $a->nodeName == 'iframe' ) {
                $link->href = $this->safeGetNodeValue( $a, 'src' );
            } else {
                $link->href = $this->safeGetNodeValue( $a, 'href' );
                $link->title = $this->safeGetNodeValue( $a, 'title' );
                $link->text = $a->textContent;
            }
        } catch ( \Exception $ex ) {
        }
        
        if ( $link->href == '#' || ! $link->href || $link->href == 'javascript:void(0);' ) {
            $onclick = $this->safeGetNodeValue( $a, 'onclick' );
            $matches = [];
            $matched = preg_match( '/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $onclick, $matches );
            if ( $matched ) {
                $link->href = $matches[0];
            }
        }
        
        $link->href = trim( $link->href );
        
        return $link->href ? $link : null;
        
    }
    
    protected function safeGetNodeValue( \DOMNode $node, $attribute ) {
        if ( $nodeItem = $node->attributes->getNamedItem( $attribute ) ) {
            return $nodeItem->nodeValue;
        } else {
            return null;
        }
    }
}