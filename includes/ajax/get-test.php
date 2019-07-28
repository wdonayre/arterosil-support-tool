<?php

add_action( 'wp_ajax_getTest', 'ajax_getTest_handler' );
add_action( 'wp_ajax_nopriv_' . 'getTest', 'ajax_getTest_handler' );

function ajax_getTest_handler() {
    global $wpdb;

    //set header to return json
    header('Content-Type: application/json');

    //var_dump(UtilProducts::instance()->getAll());
    //$pageposts = AProducts::instance()->getAll();
    //echo json_encode($pageposts);
    //$tool = new ArterosilTool();

    //$woo = new ArterosilWoocommerce();
    //$woo->init(false);
    //$returned  = $woo->api()->get('products');

    ////$tool = new ArterosilTool(false);
    ////$returned  = $tool->getProducts();

    // $returned = wc_get_order(8118);
    //echo json_encode($returned);
    //$returned = $tool->getOrder(8118);
    //var_dump($returned);
    //echo ArterosilConfig::instance()->getConfig('WOO_HOST_URL');
    exit();
}