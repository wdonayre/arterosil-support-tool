<?php

add_action( 'wp_ajax_attachCard', 'ajax_attachCard_handler' );
add_action( 'wp_ajax_nopriv_' . 'attachCard', 'ajax_attachCard_handler' );

function ajax_attachCard_handler() {
    
    //set header to return json
    header('Content-Type: application/json');

    $paymentMethodId = $_POST['paymentMethod'] ? $_POST['paymentMethod'] : null;
    $user = $_POST['user'] ? $_POST['user'] : null;

    //$userData = get_userdata($userID);
    
    $tool = new ArterosilTool(false);
    $tool->setCustomer($user['ID']);
    $attach = ($tool->getPaymentMethod($paymentMethodId))->attach(['customer' => $tool->getRawCustomerObjects()['stripe']['id']]);
    //echo json_encode($tool->getPaymentMethod($paymentMethodId));

    $args = [
        'payment_method' => $paymentMethodId,
        'email' =>   $user['user_email'],
        'name' => $user['display_name']
    ];
    
    echo json_encode($attach);

    //TODO: should be able to resolve new problems likke fiat if the status of the device  keeps changing. ]

    //$tool->setCustomer($userID);
    
    //echo json_encode($tool->SetupIntent()); 
    

    wp_die();
}