<?php

add_action( 'wp_ajax_processOrder', 'ajax_processOrder_handler' );
add_action( 'wp_ajax_nopriv_' . 'processOrder', 'ajax_processOrder_handler' );

function ajax_processOrder_handler() {
    
    //POSTS
    $param = [
        'userID' => $_POST['userID'] ? $_POST['userID'] : null,
        'line_items' => $_POST['line_items'] ? $_POST['line_items'] : null,
        'billing' => $_POST['billing'] ? $_POST['billing'] : null,
        'shipping' => $_POST['shipping'] ? $_POST['shipping'] : null,
        'cardID' => $_POST['cardID'] ? $_POST['cardID'] : null
    ];
    //$param = json_decode( json_encode($param) );


    //set header to return json
    header('Content-Type: application/json');

    /** MAKE ORDER */
    $tool = new ArterosilTool(false);
    $tool->setCustomer($param['userID']);

    $returned = $tool->createOrder([
        'billing'       => $param['billing'],
        'shipping'      => $param['shipping'],
        'line_items'    => $param['line_items'] 
    ]);

    if($returned){
        echo ($tool->processPayment($param['cardID'],$returned->get_id()));
    }

    wp_die();
}