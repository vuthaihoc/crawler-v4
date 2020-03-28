<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2020-03-16
 * Time: 05:28
 */

namespace App\Spider\Entities;


class Page {
    
    public $name;
    public $is_root;
    public $url;
    public $navigation_rules = [];
    public $data_rules = [];
    public $js = false;
    
    /**
     * Page constructor.
     *
     * @param $name
     * @param $url
     * @param bool $is_root
     * @param bool $js
     * @param array $navigation_rules
     * @param array $data_rules
     */
    public function __construct( $name, $url, $is_root = false, $js = false, $navigation_rules = [], $data_rules = [] ) {
        $this->name = $name;
        $this->is_root = $is_root;
        $this->url = $url;
        $this->js = $js;
        foreach ($navigation_rules as $page => $rule){
            $this->addNavigationRule( $page, $rule );
        }
        foreach ($data_rules as $rule){
            $this->addDataRule( $rule );
        }
    }
    
    //////// Navigation rules manipulate
    
    /**
     * @param Page|string $page
     * @param NavigationRule $rule
     *
     * @return $this
     */
    public function addNavigationRule($page, NavigationRule $rule){
        if($page instanceof Page){
            $page = $page->name;
        }
        $this->navigation_rules[$page] = $rule;
        return $this;
    }
    
    /**
     * @param Page|string $page
     * @param NavigationRule|Selector $rule
     *
     * @return Page
     */
    public function goTo($page, $rule){
        if($rule instanceof Selector){
            $rule = new NavigationRule( $rule );
        }
        return $this->addNavigationRule( $page, $rule);
    }
    
    public function removeNavigationRule($page){
        if($page instanceof Page){
            $page = $page->name;
        }
        unset( $this->navigation_rules[$page] );
        return $this;
    }
    
    public function getNavigationRules(){
        return $this->navigation_rules;
    }
    
    public function emptyNavigationRules(){
        $this->navigation_rules = [];
        return $this;
    }
    
    //////// Data rules manipulate
    
    public function addDataRule(DataRule $rule){
        $this->data_rules[$rule->name] = $rule;
        return $this;
    }
    
    public function removeDataRule(DataRule $rule){
        unset( $this->data_rules[$rule->name] );
        return $this;
    }
    
    public function emptyDataRules(){
        $this->data_rules = [];
        return $this;
    }
    
    
    
}