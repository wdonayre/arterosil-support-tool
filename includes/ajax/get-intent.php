<?php

add_action( 'wp_ajax_getIntent', 'ajax_getIntent_handler' );
add_action( 'wp_ajax_nopriv_' . 'getIntent', 'ajax_getIntent_handler' );

function ajax_getIntent_handler() {
    
    //set header to return json
    header('Content-Type: application/json');

    $userID = $_POST['userID'] ? $_POST['userID'] : null;

    $userData = get_userdata($userID);

    $tool = new ArterosilTool(false);
    //$tool->setCustomer($userID);
    
    echo json_encode($tool->SetupIntent());
    

    wp_die();
}