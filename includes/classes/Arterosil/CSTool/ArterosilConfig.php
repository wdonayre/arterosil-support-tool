<?php

namespace ZendeskCSWooCart;

class ArterosilConfig
{

    private static $instance = null;  
    private $options = null;

    public $keys = [];
    
    private function __construct(){
        $this->init();
    }

    public static function instance(){
        if(!self::$instance){
            self::$instance = new ArterosilConfig();
        }
        return self::$instance;
    }

    public function getConfig($config){
        return (getenv($config, true) ? getenv($config, true) : null);   
    }

    private function options(){
        return [
            
            // 'development_mode' => [
            //     'return' => function($item){ return get_field($item, 'option'); }
            // ],

            'wc_dev_consumer_key' => [
                'constant' => 'WOO_CONSUMER_KEY',
                'dependency' => ['key' => 'development_mode', 'value' => 1]
            ],

            'wc_dev_consumer_secret' => [
                'constant' => 'WOO_CONSUMER_SECRET',
                'dependency' => [ 'key' => 'development_mode', 'value' => true]
            ],

            'stripe_dev_publishable_key' => [
                'constant' => 'STRIPE_PUBLISHABLE_KEY',
                'dependency' => [ 'key' => 'development_mode', 'value' => true]
            ],

            'stripe_dev_secret_key' => [
                'constant' => 'STRIPE_SECRET_KEY',
                'dependency' => [ 'key' => 'development_mode', 'value' => true]
            ],

            'stripe_live_publishable_key' => [
                'constant' => 'STRIPE_PUBLISHABLE_KEY',
                'dependency' => [ 'key' => 'development_mode', 'value' => false]
            ],

            'stripe_live_secret_key' => [
                'constant' => 'STRIPE_SECRET_KEY',
                'dependency' => [ 'key' => 'development_mode', 'value' => false]
            ],

            'stripe_customer_description_text' => [
                'constant' => 'STRIPE_CUSTOMER_DESCRIPTION'
            ],

            'woocommerce_version' => [
                'constant' => 'WOO_VERSION'
            ],

            'woocommerce_rest_source' => [
                'constant' => 'WOO_REST_SOURCE',
                'return' => function($item){ return get_field($item, 'option'); }
            ],

            'woocommerce_host_url' => [
                'constant' => 'WOO_HOST_URL',
                'value' => function(){
                    if(get_field('woocommerce_rest_source', 'option') == 1) {
                        return get_field('woocommerce_host_url', 'option');
                    }
                    else {
                        return get_site_url();
                    }
                }
            ],

            'stripe_customer_id_user_meta_reference' => [
                'constant' => 'CUSTOMER_KEY_REFERENCE',
                'value' => function(){ return "_stripe_customer_id"; }
            ],

        ];
    }

    private function init(){
        
        $developmentMode = 1;

        $options = $this->options();

        foreach($options as $key => $value){
            $dependencyValue = 1;
            if(isset($options[$key]['dependency'])){
                $dependencyValue = get_field($options[$key]['dependency']['key'],'option');
                if($dependencyValue != $options[$key]['dependency']['value']){
                    $dependencyValue = 0;    
                }
            }
            
            if($dependencyValue && isset($options[$key]['constant'])){
                if(isset($options[$key]['value'])){
                    $value = $options[$key]['value']();    
                    putenv($options[$key]['constant']."=".$value);
                }
                else if(isset($options[$key]['constant'])){
                    putenv($options[$key]['constant']."=".get_field($key,'option'));    
                }
                define($options[$key]['constant'],$options[$key]['constant']);
                //$this->keys[ $options[$key]['constant'] ] = $options[$key]['constant'];
   

            }

            
        }

        return true;
    }
}