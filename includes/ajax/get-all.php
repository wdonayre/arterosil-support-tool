<?php

use ZendeskCSWooCart\ArterosilTool;

add_action( 'wp_ajax_getAll', 'ajax_getAll_handler' );
add_action( 'wp_ajax_nopriv_' . 'getAll', 'ajax_getAll_handler' );

function ajax_getAll_handler() {
    
    //set header to return json
    header('Content-Type: application/json');

    $userID = isset($_POST['userID'])?$_POST['userID']:null;
    //var_dump(get_user_meta($userID));
    //Get User Data <<<<<<<<<<<<<<<<<<<<<<<<
    // $userData = get_userdata($userID);

    



    // //Get All Products <<<<<<<<<<<<<<<<<<<<<
    $aTool = new ArterosilTool(['user_id'=>$userID]);
    $ret = $aTool->getData();
    // $customerData = $aTool->getCustomer($userID);
    // $ret['products'] = $aTool->getProducts(true,$userID);
    // $ret['cards'] = $aTool->getPaymentMethods();

    // $countriesObj = new WC_Countries();
    // //get_allowed_country_states( )
    // $ret['config']['country_list'] = $countriesObj->get_allowed_countries();
    // $ret['config']['state_list'] = $countriesObj->get_allowed_country_states();

    // if($userData){
    //     //clean password hash for security
    //     unset($userData->data->user_pass);
    //     unset($userData->data->user_activation_key);
    //     // $ret['raw'] = json_decode($customerData);
    //     $ret['customer'][$userID] = $userData;
    //     $meta = get_user_meta($userID);
    //     // $ret['customer']['_meta'] = $meta;
    //     $ret['customer']['meta'][ArterosilConfig::instance()->getConfig('CUSTOMER_KEY_REFERENCE')] = $meta[ArterosilConfig::instance()->getConfig('CUSTOMER_KEY_REFERENCE')];
    //     $ret['customer']['meta']['first_name'] = $meta['first_name'];
    //     $ret['customer']['meta']['last_name'] = $meta['last_name'];
    //     $ret['customer']['meta']['billing_address_1'] = $meta['billing_address_1'];
    //     $ret['customer']['meta']['billing_address_2'] = $meta['billing_address_2'];
    //     $ret['customer']['meta']['billing_city'] = $meta['billing_city'];
    //     $ret['customer']['meta']['billing_company'] = $meta['billing_company'];
    //     $ret['customer']['meta']['billing_country'] = $meta['billing_country'];
    //     $ret['customer']['meta']['billing_email'] = $meta['billing_email'];
    //     $ret['customer']['meta']['billing_first_name'] = $meta['billing_first_name'];
    //     $ret['customer']['meta']['billing_last_name'] = $meta['billing_last_name'];
    //     $ret['customer']['meta']['billing_phone'] = $meta['billing_phone'];
    //     $ret['customer']['meta']['billing_postcode'] = $meta['billing_postcode'];
    //     $ret['customer']['meta']['billing_state'] = $meta['billing_state'];

    //     $ret['customer']['meta']['shipping_address_1'] = $meta['shipping_address_1'];
    //     $ret['customer']['meta']['shipping_address_2'] = $meta['shipping_address_2'];
    //     $ret['customer']['meta']['shipping_city'] = $meta['shipping_city'];
    //     $ret['customer']['meta']['shipping_company'] = $meta['shipping_company'];
    //     $ret['customer']['meta']['shipping_country'] = $meta['shipping_country'];
    //     $ret['customer']['meta']['shipping_first_name'] = $meta['shipping_first_name'];
    //     $ret['customer']['meta']['shipping_last_name'] = $meta['shipping_last_name'];
    //     $ret['customer']['meta']['shipping_postcode'] = $meta['shipping_postcode'];
    //     $ret['customer']['meta']['shipping_state'] = $meta['shipping_state'];
    // }
    
    echo json_encode(array('model'=>$ret));

    wp_die();
}