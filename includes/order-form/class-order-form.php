<?php
/*
    Author: William Donayre Jr
*/

require __DIR__ . '/../../vendor/autoload.php';
use Automattic\WooCommerce\Client;
use WooCommerce\Abstracts;


class A_OrderForm {
    public $wc;
    public function __construct() {
        $this->load_dependencies();
        
        //new Arterosil_OrderForm_Endpoint();
        // $this->wc = new Client(
        //     $_SERVER['HTTP_X_FORWARDED_PROTO']."://".$_SERVER['HTTP_X_FORWARDED_HOST'], 
        //     'ck_e466b1f44af75b803491c708bfb74a99a436a19c', 
        //     'cs_e568a58a90a749816eb3c2213df087584febf420',
        //     [
        //         'version' => 'wc/v3',
        //     ]
        // );
        
        //var_dump($this->wc->get('products/7022'));
        // $v = new Abstracts(7022);

    }

    private function load_dependencies(){
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'order-form/endpoint-order-form.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'order-form/ajax-order-form.php';
    }
}


