<?php 

add_action( 'wp_ajax_updateBilling', 'ajax_updateBilling_handler' );
add_action( 'wp_ajax_nopriv_' . 'updateBilling', 'ajax_updateBilling_handler' );

function ajax_updateBilling_handler() {
    global $wpdb;
    
    $userID = isset($_POST['userID'])?$_POST['userID']:null;

    if(!isset($userID)) exit();

    $billingKeyMap = [
        'billing_address_1',
        'billing_address_2',
        'billing_city',
        'billing_company',
        'billing_country',
        'billing_email',
        'billing_first_name',
        'billing_last_name',
        'billing_phone',
        'billing_state',
        'billing_postcode'
    ];

    $billingFields = [];
    foreach($billingKeyMap as $key){
        $postData = isset($_POST[$key])?$_POST[$key]:null;
        if($postData){
            $success = update_user_meta($userID,$key,$postData);
            if($success){
                $billingFields[$key] = ['success'=>true, 'value' => $postData];
            }
            else {
                $billingFields[$key] = ['success'=>false, 'value' => $postData];
            }
        }
    }
    header('Content-Type: application/json');
    echo json_encode($billingFields);

    exit();
}