<?php

/**
 * WooCommerce REST API Client
 *
 * @category Config
 * @package  Arterosil\CSTool
 */

namespace Arterosil\CSTool;

class Config
{

    private static $instance = null;  
    
    private function __construct(){}

    public static function instance(){
        if(!self::$instance){
            self::$instance = new Config();
        }
        return self::$instance;
    }

    public function getConfig($config){
        return getenv($config, true) ?: getenv($config);   
    }
}