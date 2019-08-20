<?php

add_action( 'wp_ajax_getUser', 'ajax_getUser_handler' );
add_action( 'wp_ajax_nopriv_' . 'getUser', 'ajax_getUser_handler' );

function ajax_getUser_handler() {
    
    //set header to return json
    header('Content-Type: application/json');

    $userID = isset($_POST['userID'])?$_POST['userID']:null;

    $userData = get_userdata($userID);
    if($userData){
        
        //clean password hash for security
        unset($userData->data->user_pass);
        unset($userData->data->user_activation_key);

        $userData = (array)json_decode( json_encode($userData) );
        $meta = get_user_meta($userID);
        $userData['meta'] = $meta;
       
        
        //var_dump($userData);
        echo json_encode($userData);
    }
    else {
        echo null;
    }
    
    

    wp_die();
}