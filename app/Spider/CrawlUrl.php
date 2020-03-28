<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-08
 * Time: 02:50
 */

namespace App\Spider;


use League\Uri\Http;
use League\Uri\Uri;
use League\Uri\UriResolver;
use Symfony\Component\DomCrawler\Crawler;

class CrawlUrl {
    
    const NULL_DATETIME = "2020-01-01 00:00:00";
    const HASH_ALGORITHM = 'md5';// 64 chars
    
    protected $url; // url đầy đủ
    protected $url_hash; // hash của url sau khi chuẩn hoá
    protected $id = 0;//
    protected $step = "";
    protected $site_id = "";// site id or site name
    protected $js = false;// use javascript supported browser
    
    // parent
    protected $parent_url_hash = "";// parent url hash
    
    // status
    protected $visited = 0;// 0 1 2 3 ....
    protected $last_visited = self::NULL_DATETIME;// datetime, default `2020-01-01 00:00:00`
    protected $process_link_status = 0;// 0:init 1:processed -1:error 10:processing
    protected $process_data_status = 0;// 0:init 1:processed -1:error 10:processing
    
    /** @var Crawler */
    protected $html;
    
    /**
     * @param $url
     * @param string $step
     * @param string $id
     * @param CrawlUrl $parent
     *
     * @return CrawlUrl
     */
    public static function create( $url, $step = '', $id = '', CrawlUrl $parent = null, $js = false ) {
        $instance = new self();
        $instance->url = $url;
        $instance->step = $step;
        $instance->id = $id;
        $instance->jsRender($js);
        if($parent){
            $instance->setParent( $parent );
        }
        return $instance;
    }
    
    public function setParent( $parent ) {
        if ( $parent instanceof CrawlUrl ) {
            $this->parent_url_hash = $parent->getUrlHash();
        } elseif ( strpos( $parent, "//" ) !== false ) {
            $this->parent_url_hash = self::makeUrlHash( $parent );
        } else {
            $this->parent_url_hash = $parent;
        }
    }
    
    public function getParentUrlHash(){
        return $this->parent_url_hash;
    }
    
    public function getUrl() {
        return $this->url;
    }
    
    public function getHtml() : ?Crawler{
        return $this->html;
    }
    public function setHtml($html){
        $this->html = $html;
        return $this;
    }
    
    public function getSiteId(){
        return $this->site_id;
    }
    public function setSiteId($site_id){
        $this->site_id = $site_id;
        return $this;
    }
    
    public function getUrlHash() {
        if ( $this->url && ! $this->url_hash ) {
            $this->url_hash = self::makeUrlHash( $this->url );
        }
        
        return $this->url_hash;
    }
    
    public function getId() {
        return $this->id;
    }
    public function setId( $id ) {
        $this->id = $id;
        return $this;
    }
    
    public function getStep() {
        return $this->step;
    }
    public function setStep( $step ) {
        $this->step = $step;
        return $this;
    }
    
    public function getLastVisited(){
        return $this->last_visited;
    }
    public function setLastVisited($time){
        $this->last_visited = $time;
        return $this;
    }
    
    public function jsRender($need = null){
        if($need !== null){
            $this->js = (bool)$need;
        }
        return $this->js;
    }
    
    /**
     * Kết hợp đường dẫn tương đối vào base url
     *
     * @param $base
     * @param $uri
     *
     * @return string
     */
    public static function join( $base, $uri ): string {
        $baseUri = Http::createFromString( $base );
        $relativeUri = Http::createFromString( $uri );
        $newUri = UriResolver::resolve( $relativeUri, $baseUri );
        
        return $newUri->__toString();
    }
    
    public static function makeUrlHash( $url ) {
        return hash( self::HASH_ALGORITHM, Uri::createFromString( $url )->__toString() );
    }
    
    public function toArray(){
        $data = [
            "parent_url_hash" => $this->parent_url_hash,
            "site_id" => $this->site_id,
            "step" => $this->step,
            "url" => $this->getUrl(),
            "url_hash" => $this->getUrlHash(),
            "js" => $this->js,
            "visited" => $this->visited,
            "last_visited" => $this->last_visited ?? self::NULL_DATETIME,
            "process_link_status" => $this->process_link_status,
            "process_data_status" => $this->process_data_status,
        ];
        if($this->id){
            $data["id"] = $this->id;
        }
        return $data;
    }
    
    public static function fromObject($object) : CrawlUrl{
        $crawlUrl = self::create(
            $object->url,
            $object->step,
            $object->id
        );
        
        $crawlUrl->visited = $object->visited ?? 0;
        $crawlUrl->process_link_status = $object->process_link_status ?? 0;
        $crawlUrl->process_data_status = $object->process_data_status ?? 0;
        $crawlUrl->js = $object->js ?? 0;
        $crawlUrl->site_id = $object->site_id ?? "";
        $crawlUrl->parent_url_hash = $object->parent_url_hash ?? "";
        $crawlUrl->last_visited = $object->last_visited ?? self::NULL_DATETIME;
        
        return $crawlUrl;
    }
    
}