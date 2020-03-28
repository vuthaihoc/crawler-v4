<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2019-10-13
 * Time: 18:08
 */

namespace App\Spider\Browsers;


use App\Spider\Browsers\Phantomjs\RenderWithJs;
use App\Spider\CrawlUrl;

class BrowserManager {
    
    protected static $drivers = [];
    
    
    /**
     * @param $driver
     *
     * @return BrowserInterface
     * @throws \Exception
     */
    public static function get($driver){
        if(!isset( self::$drivers[$driver])){
            self::$drivers[$driver] = self::makeBrowser( $driver );
        }
        return self::$drivers[$driver];
    }
    
    /**
     * @param $driver
     *
     * @return BrowserInterface
     * @throws \Exception
     */
    protected static function makeBrowser($driver, $session = ''){
        switch ($driver){
            case "phantomjs":
                if($bin_phantomjs = config('browsers.drivers.phantomjs.bin')){
                    RenderWithJs::$bin = $bin_phantomjs;
                }
                return new PhantomJsLocal();
                break;
            case "guzzle":
                return new Guzzle();
                break;
            default:
                throw new \Exception("No browser match with driver " . $driver);
        }
    }
    
}