<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-16
 * Time: 05:35
 */

namespace App\Spider\Entities;


class DataRule {
    
    const TYPE_TEXT = 'text';
    const TYPE_HTML = 'html';
    const TYPE_URL = 'url';
    const TYPE_ATTRIBUTE = 'attribute';
    const TYPE_CUSTOM = 'custom';
    
    public $name;
    public $type = 'text';
    public $what = '';// attribute name or filter name
    /** @var Selector */
    public $selector;
    
    /**
     * DataRule constructor.
     *
     * @param $name
     * @param Selector $selector
     * @param string $type
     * @param null|string $what attribute name or filter name
     */
    public function __construct( $name, Selector $selector, string $type = 'text', $what = null ) {
        $this->name = $name;
        $this->type = $type;
        $this->selector = $selector;
        $this->what = $what;
    }
    
    
}