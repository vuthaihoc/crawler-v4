<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-21
 * Time: 02:02
 */


return [
    'drivers' => [
        'guzzle' => [
        
        ],
        'phantomjs' => [
            'bin' => env('BIN_PHANTOMJS', 'phantomjs'),
        ]
    ]
];