<?php

add_action( 'wp_ajax_getAll', 'ajax_getAll_handler' );
add_action( 'wp_ajax_nopriv_' . 'getAll', 'ajax_getAll_handler' );

function ajax_getAll_handler() {
    
    //set header to return json
    header('Content-Type: application/json');

    $userID = isset($_POST['userID'])?$_POST['userID']:null;

    //Get User Data <<<<<<<<<<<<<<<<<<<<<<<<
    $userData = get_userdata($userID);

    $ret = [];

    //Get All Products <<<<<<<<<<<<<<<<<<<<<
    $aTool = new ArterosilTool(false);
    $ret['products'] = $aTool->getProducts();

    if($userData){
        //clean password hash for security
        unset($userData->data->user_pass);
        unset($userData->data->user_activation_key);
        $ret['customer'][$userID] = $userData;
    }
    
    echo json_encode(array('model'=>$ret));

    wp_die();
}