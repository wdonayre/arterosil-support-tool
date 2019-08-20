<?php  

namespace ZendeskCSWooCart\Models;

use ZendeskCSWooCart\ArterosilConfig;

class Customer extends User{
    private $data = [
        'stripe_id' => ''
    ];

    public function __construct($userID){

        if(!isset($userID)) throw 'user id is required!';

        parent::__construct($userID);
        $parentData = parent::getData();
        $meta = parent::getAllMeta();

        $stripeRef = ArterosilConfig::instance()->getConfig('CUSTOMER_KEY_REFERENCE');
        $this->data['stripe_id'] = isset($meta->$stripeRef) ? reset( $meta->$stripeRef ) : '' ; 
        $this->data = array_merge($parentData,$this->data);
    }

    public function getData(){
        $parentData = parent::getData();
        return $this->data;
    }

}