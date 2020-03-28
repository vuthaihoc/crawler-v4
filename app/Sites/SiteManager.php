<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-21
 * Time: 22:24
 */

namespace App\Sites;


use App\Sites\Simple\SiteInfo;

class SiteManager {
    
    protected static $sites = [
        "itviec" => SiteInfo::class,
    ];
    
}