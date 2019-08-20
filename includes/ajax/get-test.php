<?php

use ZendeskCSWooCart\ArterosilTool;

add_action( 'wp_ajax_getTest', 'ajax_getTest_handler' );
add_action( 'wp_ajax_nopriv_' . 'getTest', 'ajax_getTest_handler' );



function ajax_getTest_handler() {
    global $wpdb;
    
    $userID = isset($_POST['userID'])?$_POST['userID']:null;
    $test = isset($_POST['test'])?$_POST['test']:null;
    //set header to return json
    header('Content-Type: application/json');


    // $tool = new ArterosilTool(false);
    // $products  = $tool->getProducts(true,$userID);
    // var_dump($products);
    // return null;
    

    /** MAKE ORDER */
    $tool = new ArterosilTool();
    //$tool->setCustomer($userID);

    if($test==="cards"){
        $tool->getAllCards();
    }
    else if($test==="createsource"){
        $res = $tool->createSource();
        var_dump($res);
    }
    //WOOCOMMERCE
    else if($test==="getProducts"){
        $res = $tool->getProducts();
        echo json_encode($res);
    }
    else if($test==="createOrder"){

    }
    else if($test==="user"){
        echo $tool->test();
    }
    else if($test="config"){
        echo ArterosilConfig::instance()->getConfig('WOO_CONSUMER_KEY').PHP_EOL;
        echo ArterosilConfig::instance()->getConfig('WOO_CONSUMER_SECRET').PHP_EOL;
        echo ArterosilConfig::instance()->getConfig('STRIPE_PUBLISHABLE_KEY').PHP_EOL;
        echo ArterosilConfig::instance()->getConfig('STRIPE_SECRET_KEY').PHP_EOL;
        echo ArterosilConfig::instance()->getConfig('STRIPE_CUSTOMER_DESCRIPTION').PHP_EOL;
        echo ArterosilConfig::instance()->getConfig('WOO_VERSION').PHP_EOL;
        echo ArterosilConfig::instance()->getConfig('WOO_REST_SOURCE').PHP_EOL;
        echo ArterosilConfig::instance()->getConfig('WOO_HOST_URL').PHP_EOL;
        echo ArterosilConfig::instance()->getConfig('CUSTOMER_KEY_REFERENCE').PHP_EOL;
        var_dump(ArterosilConfig::instance()->keys); 
    }

    exit();



    $returned = $tool->createOrder([
        'billing' => [
            'first_name' => 'test name',
            'last_name'  => 'test last name',
            'company'    => '',
            'address_1'  => '',
            'address_2'  => '',
            'city'       => '',
            'state'      => '',
            'postcode'   => '',
            'country'    => '',
            'email'      => 'test@test.com',
            'phone'      => '123451234',
        ],
        'shipping' => [
            'first_name' => 'test shippers name',
            'last_name'  => 'test shippers last name', 
            'company'    => '',
            'address_1'  => 'main st., great city',
            'address_2'  => 'address line 2 here, ',
            'city'       => '',
            'state'      => '',
            'postcode'   => '5123',
            'country'    => 'best country',
            'email'      => 'test@test.com',
            'phone'      => '123451234',    
        ],
        'line_items' => [
            [
                'product_id' => 222,
                'quantity' => 2
            ],
            [
                'product_id' => 170,
                'quantity' => 1
            ]
        ]
    ]);

    if($returned){
        //process payment
        $res = $tool->processPayment('card_1F1sa6AKZbexXQ0j3dXdZqjF',$returned->get_id());
        var_dump($res);
    }

    exit();
}