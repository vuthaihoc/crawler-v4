<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2019-10-12
 * Time: 02:26
 */

namespace App\Spider\Browsers;


use App\Spider\Browsers\Phantomjs\RenderWithJs;

class PhantomJsLocal implements BrowserInterface {
    
    public function getHtml( $url ) {
        $response = RenderWithJs::render( $url );
        return $response['html'];
    }
    
}