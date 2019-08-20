<?php

add_action( 'wp_ajax_createCustomerByPaymentMethod', 'ajax_createCustomerByPaymentMethod_handler' );
add_action( 'wp_ajax_nopriv_' . 'createCustomerByPaymentMethod', 'ajax_createCustomerByPaymentMethod_handler' );

function ajax_createCustomerByPaymentMethod_handler() {
    
    //set header to return json
    header('Content-Type: application/json');

    $paymentMethodId = $_POST['paymentMethod'] ? $_POST['paymentMethod'] : null;
    $user = $_POST['user'] ? $_POST['user'] : null;

    //$userData = get_userdata($userID);
    
    $tool = new ArterosilTool(false);
    $args = [
        'payment_method' => $paymentMethodId,
        'email' =>   $user['user_email'],
        'name' => $user['display_name']
    ];
    
    echo json_encode($tool->createCustomerByPaymentMethod($args));
    //$tool->setCustomer($userID);
    
    //echo json_encode($tool->SetupIntent());
    

    wp_die();
}