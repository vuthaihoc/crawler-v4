<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2019-10-12
 * Time: 01:52
 */

namespace App\Spider\Browsers;

interface BrowserInterface {
    
    public function getHtml($url);
    
}