<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2019-10-13
 * Time: 18:35
 */

namespace App\Spider\Browsers;


use GuzzleHttp\Client;

class Guzzle implements BrowserInterface {
    
    protected $client;
    
    /**
     * Guzzle constructor.
     *
     * @param $client
     */
    public function __construct( ?Client $client = null ) {
        if($client){
            $this->client = $client;
        }else{
            $this->client = new Client();
        }
    }
    
    
    public function getHtml( $url ) {
        $response = $this->client->get( $url );
        return $response->getBody()->getContents();
    }
    
}