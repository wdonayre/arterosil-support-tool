<?php

namespace Arterosil\CSTool;

use Automattic\WooCommerce\Client;

class ArterosilWoocommerce
{
    private $woocommerce = null;

    public function __construct(){}

    public function init(){
        $this->woocommerce = new Client(
            Config::instance()->getConfig('WOO_HOST_URL'), 
            Config::instance()->getConfig('WOO_CONSUMER_KEY'), 
            Config::instance()->getConfig('WOO_CONSUMER_SECRET'),
            [
                'wp_api' => true,
                'version' => Config::instance()->getConfig('WOO_VERSION'),
                'query_string_auth' => true
            ]
        );
    }

    /**
     * Return rest api service reference
    */
    public function api(){
        return $this->woocommerce;
    }

    /**
     * Woocommerce rest 'get()' wrapper
    */
    public function get($path){
        try{
            return $this->woocommerce->get($path);
        } catch(\Exception $e){
            return json_encode(
                array(
                    'error' => true,
                    'message' => $e->getMessage()
                )
            );
        }
        
    }

    /**
     * Woocommerce rest 'post()' wrapper
    */
    public function post($path,$data=null){
        return $this->woocommerce->post($path,$data);
    }

     /**
     * Woocommerce rest 'put()' wrapper
    */
    public function put($path,$data){
        try{
            return $this->woocommerce->put($path,$data);
        } catch(\Exception $e){
            echo json_encode(
                array(
                    'message' => $e->getMessage()
                )
            );
        }
        
    }



}