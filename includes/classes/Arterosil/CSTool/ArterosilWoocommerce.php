<?php

namespace ZendeskCSWooCart;

use Automattic\WooCommerce\Client;

class ArterosilWoocommerce
{
    private $woocommerce = null;
    private $useRest = true;

    public function __construct(){}

    public function init($useRest=true){
        $this->useRest = $useRest;
        if($this->useRest){
            $this->woocommerce = new Client(
                ArterosilConfig::instance()->getConfig('WOO_HOST_URL'), 
                ArterosilConfig::instance()->getConfig('WOO_CONSUMER_KEY'), 
                ArterosilConfig::instance()->getConfig('WOO_CONSUMER_SECRET'),
                [
                    'wp_api' => true,
                    'version' => "wc/v3",
                    'query_string_auth' => true
                ]
            );
        }
    }

    /**
     * Return rest api service reference
    */
    public function api(){
        if($this->useRest){
            return $this->woocommerce;
        }
        return ArterosilWoocommerceNoneRestApi::instance();
    }

    /**
     * Woocommerce rest 'get()' wrapper
    */
    public function get($path){
        // var_dump($this->useRest);
        try{
            if($this->useRest){
                return $this->woocommerce->get($path);
            }
            return  ArterosilWoocommerceNoneRestApi::instance()->get($path);
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
        if($this->useRest){
            return $this->woocommerce->post($path,$data);
        }
        return null;
    }

     /**
     * Woocommerce rest 'put()' wrapper
    */
    public function put($path,$data){
        try{
            if($this->useRest){
                return $this->woocommerce->put($path,$data);
            }
            return ArterosilWoocommerceNoneRestApi::instance()->put($path,$data);
        } catch(\Exception $e){
            echo json_encode(
                array(
                    'message' => $e->getMessage()
                )
            );
        }
        
    }
}