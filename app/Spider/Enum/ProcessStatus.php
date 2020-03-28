<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-08
 * Time: 03:13
 */

namespace App\Spider\Enum;


use MyCLabs\Enum\Enum;

class ProcessStatus extends Enum {
    
    const ERROR = -1;
    const INIT = 0;
    const SUCCESS = 1;
    const PROCESSING = 10;
    
}