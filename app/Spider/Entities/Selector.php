<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-16
 * Time: 05:32
 */

namespace App\Spider\Entities;


class Selector {
    
    const TYPE_CSS = 'css';
    const TYPE_XPATH = 'xpath';
    
    public $type = 'css';
    public $content = '';
    public $multiple = false;
    
    /**
     * Selector constructor.
     *
     * @param string $type
     * @param string $content
     * @param bool $multiple
     */
    public function __construct( string $type, string $content, bool $multiple = false) {
        $this->type = $type;
        $this->content = $content;
        $this->multiple = $multiple;
    }
    
    public static function css($content, $multiple = false){
        return new self(self::TYPE_CSS, $content, $multiple);
    }
    
    public static function xpath($content, $multiple = false){
        return new self(self::TYPE_XPATH, $content, $multiple);
    }
    
    /**
     * Kiểm tra selector hiện tại có đạt chuẩn ko
     * @return bool
     */
    public function isValid() : bool {
        return true;
    }
    
}