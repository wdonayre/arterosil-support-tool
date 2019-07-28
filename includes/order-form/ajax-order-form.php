<?php

add_action( 'wp_ajax_getCustomers', 'ajax_getCustomers_handler' );
add_action( 'wp_ajax_nopriv_' . 'getCustomers', 'ajax_getCustomers_handler' );

function ajax_getCustomers_handler() {
    
    //set header to return json
    header('Content-Type: application/json');

    $args = array(
        'role' => '',
        'orderby' => 'display_name',
        'order' => 'ASC',
        'search' => ($_POST['q'])?"*".$_POST['q']."*":"*"
    );

    $users = get_users($args);
    if(isset($users)){
        $ret = [];
        foreach($users as $user){
            $ret[] = array(
                'id' => $user->ID,
                'text' => $user->data->display_name,
                'user_login' => $user->data->user_nicename,
                'user_email' => $user->data->user_email,
                'display_name' => $user->data->display_name
            );
        }
        echo json_encode($ret);
    } else {
        echo null;
    }

    wp_die();
}