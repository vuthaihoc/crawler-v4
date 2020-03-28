<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-16
 * Time: 05:30
 */

namespace App\Spider\Entities;


class NavigationRule {
    
    const TYPE_LINK = 'link';
    
    public $type = 'link';
    
    /** @var Selector */
    public $selector;
    
    /**
     * NavigationRule constructor.
     *
     * @param Selector $selector
     * @param string $type
     * @param bool $is_multiple
     */
    public function __construct( Selector $selector, string $type = 'link') {
        $this->type = $type;
        $this->selector = $selector;
    }
    
}