<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-16
 * Time: 05:06
 */

namespace App\Sites\Simple;

use App\Spider\Entities\DataRule;
use App\Spider\Entities\Page;
use App\Spider\Entities\Selector;

class SiteInfo {
    
    protected $name;
    
    protected $pages = [];
    
    protected $delay = 0;
    
    /**
     * SiteInfo constructor.
     *
     * @param array $pages
     */
    public function __construct( $name ) {
        $this->name = $name;
        $this->init_pages();
    }
    
    protected function init_pages() {
        
        // Define pages
        $root = new Page( 'root', 'https://itviec.com/it-jobs/php', true );
        $detail = new Page( 'detail', 'https://itviec.com/it-jobs/php-developers-mysql-net-trung-tam-bao-hanh-dell-tai-viet-nam-digipro-1424' );
        $company = new Page( "company", 'https://itviec.com/companies/relia-systems?utm_campaign=gsn_brand&utm_medium=key_cpc&utm_source=google%2Fha-noi', false );
        
        // Define get data rules
        $company->addDataRule( new DataRule( "company_name", new Selector( "css", "div.name-and-info h1")));
        $company->addDataRule( new DataRule( "company_location", new Selector( "css", "div.name-and-info span")));
        $company->addDataRule( new DataRule( "company_logo", new Selector( "css", "div.headers .logo img"), DataRule::TYPE_ATTRIBUTE, 'data-src'));
        
        // Define Navigation rules
        $root->goTo(
            $detail,
            Selector::css("#jobs h2", true )
        );
        $root->goTo(
            $root,
            Selector::css( "#show_more .more-jobs-link", 'link' )
        );
        $detail->goTo( $company, Selector::xpath( "//div[@class='side_bar']//a[text()='View our company page']"));
    
    
        // Add to site
        $this->pages[ $root->name ] = $root;
        $this->pages[ $detail->name ] = $detail;
        $this->pages[ $company->name ] = $company;
    }
    
    public function getPages() {
        return $this->pages;
    }
    
    public function getPage( $name ): ?Page {
        if ( isset( $this->pages[ $name ] ) ) {
            return $this->pages[ $name ];
        }
        
        return null;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getDelay(){
        return $this->delay;
    }
    
}