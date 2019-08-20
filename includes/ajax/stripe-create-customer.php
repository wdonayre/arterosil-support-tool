<?php

add_action( 'wp_ajax_createCustomer', 'ajax_createCustomer_handler' );
add_action( 'wp_ajax_nopriv_' . 'createCustomer', 'ajax_createCustomer_handler' );

function ajax_createCustomer_handler() {
    
    //set header to return json
    header('Content-Type: application/json');

    $paymentMethodId = $_POST['paymentMethood'] ? $_POST['paymentMethood'] : null;

    //$userData = get_userdata($userID);

    $tool = new ArterosilTool(false);
    echo json_encode($tool->createCustomerByPaymentMethod($paymentMethodId));
    //$tool->setCustomer($userID);
    
    //echo json_encode($tool->SetupIntent());
    

    wp_die();
}