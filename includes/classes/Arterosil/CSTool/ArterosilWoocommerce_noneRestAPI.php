<?php

namespace ZendeskCSWooCart;

/**
 * This class will be used if Woocommerce Rest option is disabled or false
*/
class ArterosilWoocommerceNoneRestApi
{
   private static $instance = null;

    public function __construct(){}

    public static function instance(){
        if(!self::$instance){
            self::$instance = new ArterosilWoocommerceNoneRestApi();
        }
        return self::$instance;
    }

    public function get($path){
        //orders+[?\\]

        // $match = preg_match('/orders+[?\/]/',$path);


        if(preg_match('/orders+[?\/]/',$path)){ //if orders EP with param
            $orderID = str_replace("orders/","",$path);
            return wc_get_order( $orderID );
        }
        else if(preg_match('/orders/',$path)){ //if orders EP w/o param
            return wc_get_orders(array());
        } 


        else if(preg_match('/products+[?\/]/',$path)){ //if products EP with param{

        }
        else if(preg_match('/products/',$path)){ //if products EP with param{
            global $wpdb;
            //TODO use festi expose class to fetch product by role getPriceByUserId
            $queryStr = "
                SELECT wp_posts.* FROM wp_posts  WHERE 1=1 
                AND wp_posts.post_type = 'product'
                AND (wp_posts.post_status = 'publish' )
                ORDER BY wp_posts.post_date DESC 
            ";

            $products = $wpdb->get_results($queryStr, OBJECT);

            
            foreach($products as $product){
                $wcProduct = new WC_Product($product->ID);
                $wcData = $wcProduct->get_data();
                //var_dump(json_encode($wcData['meta_data']));
                // $wcDataNew = wp_list_pluck($wcData->meta_data,)
                $allowed = [];
                foreach($wcData['meta_data'] as $value){
                    //_wc_restrictions_allowed
                    if($value->key === "_wc_restrictions_allowed"){
                        $allowed[$value->value] = $value;
                    }
                }

                $ret[$product->ID] = [
                    'wpProduct' => $product,
                    'wcProduct' => $wcData,
                    'rolePricing' => json_decode($wcProduct->get_meta('festiUserRolePrices')),
                    'allowed' => ($allowed)
                ];

            }
        }

        else if(preg_match('/customers+[?\/]/',$path)){ //if customers EP with param
            $customerID = str_replace("customers/","",$path);
            
            $customerObj = new WC_Customer($customerID);
            
            if(!empty($customerObj)){
                $ret = $customerObj;
            }
            
        }


        return $ret;
    }

    /**
     * PUT Method
    */
    public function put($path, $data){
        if(preg_match('/customers+[?\/]/',$path)){ //if orders EP with param
            $customerID = str_replace("customers/","",$path);
            
            $customerObj->update_meta_data(ArterosilConfig::instance()->getConfig('CUSTOMER_KEY_REFERENCE'), 'this is test');
            $customerObj->save_meta_data();
            

            //return wc_get_order( $orderID );
        }
        if(preg_match('/orders+[?\/]/',$path)){
            $orderID = str_replace("orders/","",$path);   
            $orderObj = wc_get_order( $orderID );
            //TODO should be dynamic on this part
            return $orderObj->update_status('processing', 'Stripe payment successful: <a target="_blank" href="'.$data['receipt_url'].'">'.$data['charge_id'].'</a>');
        }
    }




}