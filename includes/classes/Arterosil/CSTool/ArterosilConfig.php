<?php

class ArterosilConfig
{

    private static $instance = null;  
    private $options = null;
    
    private function __construct(){
        $this->checkThemeOptions();
    }

    public static function instance(){
        if(!self::$instance){
            self::$instance = new ArterosilConfig();
        }
        return self::$instance;
    }

    public function getConfig($config){
        return (getenv($config, true) ? getenv($config, true) : null);   
    }

    private function checkThemeOptions(){
        
        //check ACF dependent options if exist    
        $optionsToCheck = [
            //get_field('field_name', 'option')
            'development_mode' => 'DEVELOPMENT_MODE',
            'wc_dev_consumer_key' => 'WOO_CONSUMER_KEY',
            'wc_dev_consumer_secret' => 'WOO_CONSUMER_SECRET',
            'stripe_live_publishable_key' => 'STRIPE_PUBLISHABLE_KEY',
            'stripe_live_secret_key' => 'STRIPE_SECRET_KEY',
            'stripe_dev_publishable_key' => 'STRIPE_PUBLISHABLE_KEY',
            'stripe_dev_secret_key' => 'STRIPE_SECRET_KEY',
            'stripe_customer_description_text' => 'STRIPE_CUSTOMER_DESCRIPTION',
            'woocommerce_version' => 'WOO_VERSION'
        ];

        $developmentMode = 1;
        foreach($optionsToCheck as $item => $key){

            if($item === 'development_mode'){
                $developmentMode = get_field($item, 'option');
            } else if(  $item === 'wc_dev_consumer_key' ||
                        $item === 'wc_dev_consumer_secret' ||
                        $item === 'stripe_dev_publishable_key' ||
                        $item === 'stripe_dev_secret_key'){

                if($developmentMode){
                    putenv($key."=".get_field($item, 'option'));    
                } //just do nothing here if else

            } else if( $item === 'stripe_live_publishable_key' || $item === 'stripe_live_secret_key'){

                if(!$developmentMode){
                    putenv($key."=".get_field($item, 'option'));    
                } //just do nothing here if else   

            } else {
                putenv($key."=".get_field($item, 'option'));       
            }
        }

        //Manul config entry
        putenv("WOO_HOST_URL=pwdcdev1.com");

        return true;
    }
}