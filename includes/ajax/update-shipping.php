<?php 

add_action( 'wp_ajax_updateShipping', 'ajax_updateShipping_handler' );
add_action( 'wp_ajax_nopriv_' . 'updateShipping', 'ajax_updateShipping_handler' );

function ajax_updateShipping_handler() {
    global $wpdb;
    
    $userID = isset($_POST['userID'])?$_POST['userID']:null;

    if(!isset($userID)) exit();

    $shippingKeyMap = [
        'shipping_address_1',
        'shipping_address_2',
        'shipping_city',
        'shipping_company',
        'shipping_country',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_state',
        'shipping_postcode'
    ];

    $shippingFields = [];
    foreach($shippingKeyMap as $key){
        $postData = isset($_POST[$key])?$_POST[$key]:null;
        if($postData){
            $success = update_user_meta($userID,$key,$postData);
            if($success){
                $shippingFields[$key] = ['success'=>true, 'value' => $postData];
            }
            else {
                $shippingFields[$key] = ['success'=>false, 'value' => $postData];
            }
        }
    }
    header('Content-Type: application/json');
    echo json_encode($shippingFields);

    exit();
}