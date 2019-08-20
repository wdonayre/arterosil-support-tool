<?php

namespace ZendeskCSWooCart\Models;

use ZendeskCSWooCart\ArterosilConfig;

class User
{

    private $remoteSource = 0;
    private $config = null;

    private $testData = [];
    private $allMeta = [];

    private $data = [
        'ID' => '',
        'user_login' => '',
        'roles' => [],
        'name' => '',
        'first_name' => '',
        'last_name' => '',
        'user_email' => '',
        // 'stripe_id' => '',
        'billing' => [],
        'shipping' => []
    ];

    private $userData = [];

    public function __construct($userID, $remoteSourceOverride=null){

        if(empty($userID)) throw 'user id is missing.';

        $this->data['ID'] = $userID;

        $this->config = ArterosilConfig::instance();
        $keys = $this->config->keys;

        //set remote source by config
        $this->setRemoteSource(intval($this->config->getConfig( 'WOO_REST_SOURCE' )));

        if(isset($remoteSourceOverride)){
            $this->setRemoteSource( intval($remoteSourceOverride) );
        }

        if(!$this->remoteSource){
            $userData = get_userdata($userID);
            
            unset($userData->data->user_pass);
            unset($userData->data->user_activation_key);
            
            if($userData){
                $userData = (array)json_decode( json_encode($userData) );
                $meta = get_user_meta($userID);
                $userData['meta'] = $meta;
            }
            $this->mapUserData( $userData );
        }
        else{
            //TODO should be able to get from source
            $response = wp_remote_post(
                $this->config->getConfig('WOO_HOST_URL').'/wp-admin/admin-ajax.php',
                [
                    'method' => 'POST',
                    'blocking' => true,
                    'body' => [
                        'action' => 'getUser',
                        'userID' => $this->data['ID']
                    ],
                    'headers' => []
                ]
            );
            $this->mapUserData( json_decode($response['body']) );
        }
    }

    /**
     * Get All Meta
    */
    public function getAllMeta(){
        return $this->allMeta;
    }

    /**
     * Set Remote Flag to false
     * @param bool flag
    */
    public function setRemoteSource($flag){
        $this->remoteSource = $flag;
    }

    /**
     * Map User Data from source to this model properties
    */
    private function mapUserData($data){
        $data = json_decode( json_encode($data) );
        foreach($data as $key=>$value){
            if($key === 'data'){
                $this->data['user_login'] = $value->user_login;
                $this->data['user_email'] = $value->user_email;
                $this->data['display_name'] = $value->display_name;
            }
            else if($key === 'roles'){
                $this->data['roles'] = $value;
            }
            else if($key === 'meta'){
                
                //have a copy of meta
                $this->allMeta = $value;

                $this->data['first_name']       = isset($value->first_name)         ? reset($value->first_name)        : '' ;
                $this->data['last_name']        = isset($value->last_name)          ? reset($value->last_name)         : '' ;
                $this->data['default_role']     = isset($value->my_default_role)    ? reset($value->my_default_role)   : '';

                $stripeRef = $this->config->getConfig('CUSTOMER_KEY_REFERENCE');
                //$this->data['stripe_id']        = isset($value->stripeRef) ? reset( $value->$stripeRef ) : '' ;
                
                //billing
                $this->data['billing']['billing_email']         = isset($value->billing_email)      ? reset( $value->billing_email )        : '' ;
                $this->data['billing']['billing_first_name']    = isset($value->billing_first_name) ? reset( $value->billing_first_name )   : '' ;
                $this->data['billing']['billing_last_name']     = isset($value->billing_last_name)  ? reset( $value->billing_last_name )    : '' ;
                $this->data['billing']['billing_company']       = isset($value->billing_company)    ? reset( $value->billing_company )      : '' ;
                $this->data['billing']['billing_address_1']     = isset($value->billing_address_1)  ? reset( $value->billing_address_1 )    : '' ;
                $this->data['billing']['billing_address_2']     = isset($value->billing_address_2)  ? reset( $value->billing_address_2 )    : '' ;
                $this->data['billing']['billing_city']          = isset($value->billing_city)       ? reset( $value->billing_city )         : '' ;
                $this->data['billing']['billing_postcode']      = isset($value->billing_postcode)   ? reset( $value->billing_postcode )     : '' ;
                $this->data['billing']['billing_country']       = isset($value->billing_country)    ? reset( $value->billing_country )      : '' ;
                $this->data['billing']['billing_state']         = isset($value->billing_state)      ? reset( $value->billing_state )        : '' ;
                $this->data['billing']['billing_phone']         = isset($value->billing_phone)      ? reset( $value->billing_phone )        : '' ;

                //shipping
                $this->data['shipping']['shipping_first_name']  = isset($value->shipping_first_name)    ? reset( $value->shipping_first_name )  : '' ;
                $this->data['shipping']['shipping_last_name']   = isset($value->shipping_last_name)     ? reset( $value->shipping_last_name )   : '' ;
                $this->data['shipping']['shipping_company']     = isset($value->shipping_company)       ? reset( $value->shipping_company )     : '' ;
                $this->data['shipping']['shipping_address_1']   = isset($value->shipping_address_1)     ? reset( $value->shipping_address_1 )   : '' ;
                $this->data['shipping']['shipping_address_2']   = isset($value->shipping_address_2)     ? reset( $value->shipping_address_2 )   : '' ;
                $this->data['shipping']['shipping_city']        = isset($value->shipping_city)          ? reset( $value->shipping_city )        : '' ;
                $this->data['shipping']['shipping_postcode']    = isset($value->shipping_postcode)      ? reset( $value->shipping_postcode )    : '' ;
                $this->data['shipping']['shipping_country']     = isset($value->shipping_country)       ? reset( $value->shipping_country )     : '' ;
                $this->data['shipping']['shipping_state']       = isset($value->shipping_state)         ? reset( $value->shipping_state )       : '' ; 
                
            }
        }    
    }

    /**
     * Get Model Data
    */
    public function getData($key=null){
        if(isset($key)){
            return $this->data[$key];    
        }
        else {
            return $this->data;
        }
        
    }

    // public function test(){
    //     //return json_encode($this->testData);
    //     return json_encode($this->data);
    // }

    
}