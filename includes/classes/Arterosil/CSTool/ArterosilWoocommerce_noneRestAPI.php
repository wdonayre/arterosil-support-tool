<?php

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

        $match = preg_match('/orders+[?\/]/',$path);


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
            
            $queryStr = "
                SELECT wp_posts.* FROM wp_posts  WHERE 1=1 
                AND wp_posts.post_type = 'product'
                AND (wp_posts.post_status = 'publish' )
                ORDER BY wp_posts.post_date DESC 
            ";

            $products = $wpdb->get_results($queryStr, OBJECT);

            
            foreach($products as $product){
                $wcProduct = new WC_Product($product->ID);
                $ret[$product->ID] = [
                    'wpProduct' => $product,
                    'wcProduct' => $wcProduct->get_data()
                ];

            }

             
        }


        return $ret;
    }



}