<?php

namespace ZendeskCSWooCart;

use ZendeskCSWooCart\Models\User;
use ZendeskCSWooCart\Models\Customer;
use ZendeskCSWooCart\Models\Agent;

// use Arterosil\CSTool\ArterosilWoocommerce;
// use Arterosil\CSTool\ArterosilStripe;

require_once plugin_dir_path( dirname( __FILE__ ) ).'CSTool/ArterosilWoocommerce_noneRestAPI.php';
require_once plugin_dir_path( dirname( __FILE__ ) ).'CSTool/ArterosilWoocommerce.php';
require_once (plugin_dir_path( dirname( __FILE__ ) ).'CSTool/ArterosilStripe.php');
// require_once (plugin_dir_path( dirname( __FILE__ ) ).'CSTool/models/models.php');

class ArterosilTool
{
    private $woocommerce;
    private $stripe;
    private $customerObj;
    private $customerStripeObj;

    private $customer;
    private $agent;

    public function __construct($args /*$wooRest=true,$stripeRest=true,$userID=null*/){
        
        $args = [
            'use_woo_rest'      => isset($args['use_woo_rest'])      ? $args['use_woo_rest']     :true,
            'use_stripe_rest'   => isset($args['use_stripe_rest'])   ? $args['use_stripe_rest']  :true,
            'user_id'           => isset($args['user_id'])           ? $args['user_id']          :null
        ];
        
        $this->init($args);
    }

    private function init($args){
        /******************************************
         * Initialize Users (Customer and Agent)
        *****************************************/
        $this->agent = new Agent();
        $this->customer = new Customer($args['user_id']);

        /******************************************
         * Initialize woocommerce rest api arterosil class wrapper
        *****************************************/
        $this->woocommerce = new ArterosilWoocommerce();
        $this->woocommerce->init( $args['use_woo_rest'] );
       

        /******************************************
         * Initialize Stripe
        *****************************************/
        $this->stripe = new ArterosilStripe();

    }

    /**
     * Process Payment
    */
    public function processPayment($sourceID, $orderID){ 
        
        //get customer object
        //$customerObj = json_decode($this->getCustomer($customerID));//$this->woocommerce->get('customers/'.$customerID);
        
        //get order object
        $orderObj = $this->woocommerce->get('orders/'.$orderID);
        
        //get source object
        //$sourceObj = $this->stripe->getSource($sourceID);// $this->stripe->getSources($customerObj->email,null,false);
        //$pmObj = $this->stripe->getPaymentMethod();
        
        //process payment
        $paymentObj = $this->stripe->createPaymentIntent([
            'amount' => intval($orderObj->total)*100,
            'currency' => $orderObj->currency,
            'payment_method' => $sourceID,
            'statement_descriptor' => 'Order# '.$orderID,
            'description' => 'Arterosil Payment for ORDER#'.$orderID
        ]);

        if($paymentObj['status'] === 'successful'){
            $charge = $paymentObj['data']->charges->data[0];
            if($charge && $charge->captured){
                //we update woocommerce order status here:
                $data = [
                    'status' => 'processing',
                    'payment_method' => 'stripe',
                    'payment_method_title' => 'Credit Card',
                    'charge_id' => $charge->id,
                    'receipt_url' => $charge->receipt_url
                ];
                $ret = $this->woocommerce->put('orders/'.$orderID,$data);
               
                if($ret) return true;
                else return false;
            } else {
                return "TODO: no charge value need to investigate";
            }
    
            return json_encode($paymentObj);

        } 
        else if($paymentObj['status'] === 'error'){
            
            $body = $paymentObj['data']->getJsonBody();
            
            $err  = $body['error'];
            $err['WCOrderObject'] = $orderObj->get_data();

            
            $orderObj->add_order_note('Stripe payment attempt failed:<br> <span style="color:red;">'.$err['message'].'</span><br><span style="text-transform:capitalize;">'.$err['payment_method']['card']['brand'].'</span> Card used ends with <b>'.$err['payment_method']['card']['last4'].'</b></b>',false,true);
            $orderObj->update_status('on-hold', '');

            wp_send_json_error($err,400);
        }
    }

    public function test(){
        $user = new Agent();
        return json_encode($user->getData());
    }

    public function processPaymentNewSource(){
        //TODO - should be able to use striple UI elements in capturing cards
    }

    public function createOrder($orderData){
        if(!$this->customerObj) return null; //exit when no active customer object is found in the model

        $args = [
            "customer_id" => $this->customerObj->get_id()
        ];

        $orderObj = wc_create_order($args);
        $orderObj->add_order_note('Order Created',false,true);
        if(!empty($orderData['line_items'])){
            foreach($orderData['line_items'] as $order){
                $product = wc_get_product($order['product_id']);  
                $customerPrice = $this->getPriceByCustomer($product); 
                $subTotal = ( $order['quantity'] * strval($customerPrice) );
                $orderObj->add_product($product,$order['quantity'],['subtotal'=> $subTotal, 'total'=>$subTotal]);  //create util function that computes price based on user role
            }
        }
        else {
            return null;
        }

        //set billing
        if(!empty($orderData['billing'])){
            $orderObj->set_billing_first_name($orderData['billing']['first_name']);
            $orderObj->set_billing_last_name($orderData['billing']['last_name']);
            $orderObj->set_billing_company($orderData['billing']['company']);
            $orderObj->set_billing_address_1($orderData['billing']['address_1']);
            $orderObj->set_billing_address_2($orderData['billing']['address_2']);
            $orderObj->set_billing_city($orderData['billing']['city']);
            $orderObj->set_billing_state($orderData['billing']['state']);
            $orderObj->set_billing_postcode($orderData['billing']['postcode']);
            $orderObj->set_billing_country($orderData['billing']['country']);
            $orderObj->set_billing_email($orderData['billing']['email']);
            $orderObj->set_billing_phone($orderData['billing']['phone']);
        }
        //set shipping
        
        if(!empty($orderData['shipping'])){
            $orderObj->set_shipping_first_name($orderData['shipping']['first_name']);
            $orderObj->set_shipping_last_name($orderData['shipping']['last_name']);
            $orderObj->set_shipping_company($orderData['shipping']['company']);
            $orderObj->set_shipping_address_1($orderData['shipping']['address_1']);
            $orderObj->set_shipping_address_2($orderData['shipping']['address_2']);
            $orderObj->set_shipping_city($orderData['shipping']['city']);
            $orderObj->set_shipping_state($orderData['shipping']['state']);
            $orderObj->set_shipping_postcode($orderData['shipping']['postcode']);
            $orderObj->set_shipping_country($orderData['shipping']['country']);
        }

        $orderObj->calculate_totals();
        $orderObj->save();

        return $orderObj;
    }
    public function getOrder($orderID){
        $ret = $this->woocommerce->get('orders/'.$orderID);    
        return $ret;
    }

    /**
     * Return raw customer objects from cached woocommerce and stripe
    */
    public function getRawCustomerObjects(){
        return [
            'wc' => $this->customerObj,
            'stripe' => $this->customerStripeObj
        ];
    }

    /**
     * Create Customer By Payment Method
    */
    public function createCustomerByPaymentMethod($args){
        $stripeCustomerObj = $this->stripe->createCustomer($args);
        //set default source
        //$this->stripe->updateCustomer(['default_source' => $args['payment_method']]);
        return $stripeCustomerObj;
    }

    /**
     * Return customer object from woocommerce
     * (automatically creates stripe customer object when called and if non existing)
    */
    public function getCustomer($customerID,$cached=false){
        //ensure that customer object in wc exist in stripe too
        if(!$this->customerObj || !$cached){
            $customerObj = ($this->woocommerce->get('customers/'.$customerID));
            $this->customerObj = $customerObj;
        } else {
            $customerObj = $this->customerObj;
        }

        if(!$customerObj) return false; //if no customer id exist
        // $this->customerObj = $customerObj;
        
        //prepare data if in case we need to create new customer in stripe 
        $data=[];
        var_dump($customerObj);
        $data['name'] = $customerObj->get_first_name().' '.$customerObj->get_last_name();
        $data['email'] = $customerObj->get_email();
        $data['description'] = ArterosilConfig::instance()->getConfig('STRIPE_CUSTOMER_DESCRIPTION') . " - " .$data['name'];
        
        $customerStripeID = $this->getStripeCustomerID();

        if($customerStripeID){

            //verify if customer stripe id exists from stripe server
            $stripeCustomerObj = $this->stripe->getCustomerById($customerStripeID);

            if(!$stripeCustomerObj || ($stripeCustomerObj->deleted)){ //if it doesnt exist from stripe, we create a new struoe customer object out of it
                $stripeCustomerObj = $this->stripe->createCustomer($data);
                $customerObj->update_meta_data(ArterosilConfig::instance()->getConfig('CUSTOMER_KEY_REFERENCE'), $stripeCustomerObj->id);
                $customerObj->save_meta_data();
            } 

        } else {
            $stripeCustomerObj = $this->stripe->createCustomer($data);
            $customerObj->update_meta_data(ArterosilConfig::instance()->getConfig('CUSTOMER_KEY_REFERENCE'), $stripeCustomerObj->id);
            $customerObj->save_meta_data();
        }
        
        $this->customerStripeObj = $stripeCustomerObj;

        return json_encode([
            'wcCustomer'=>$customerObj->get_data(),
            'stripeCustomer' => ($stripeCustomerObj)
        ]);

    }

    /**
     * Set Active Customer
    */
    public function setCustomer($customerID){
        $this->getCustomer($customerID); 
    }

    /**
     * Get Active Customer
    */
    public function getActiveCustomer(){
        return $this->customerObj;
    }

    /**
     * Get Products
    */
    public function getProducts($args=[]/*$autoPriceMap=false,$userID=null, $args=null*/){
        $args = [
            'auto_price_map' => isset($args['auto_price_map'])?$args['auto_price_map']:false,
            'user_id' => isset($args['user_id'])?$args['user_id']:null,
        ];
        $products = $this->woocommerce->get('products');
        return $products;
        if(!$args['auto_price_map']){
            return $products;
        }
        else {
            if($this->customer->getData('ID')){
        //         $this->setCustomer($userID);
                $role = $this->customer->getData('default_role');
                $newProducts = [];
                
                foreach($products as $product){
                    $product['rolePrice'] = ($product['rolePricing']->$role) ? $product['rolePricing']->$role : $product['wcProduct']['price'];
                    
        //             if(count($product['allowed'])>0){
        //                 if(isset($product['allowed'][$role])){
        //                     $newProducts[$product['wpProduct']->ID] = $product;
        //                 }  
        //             }
        //             else {
        //                 $newProducts[$product['wpProduct']->ID] = $product;
        //             }

        //             unset($newProducts[$product['wpProduct']->ID]['rolePricing']);
        //             unset($newProducts[$product['wpProduct']->ID]['allowed']);
        //             //$product['rolePrice'] = $product['rolePricing']->$role;
        //             //$newProducts[$product['wpProduct']->ID] = $product;
                }
                $products = $newProducts;
            }
            return $products;
        }
        
    }
    
    // private function returnProduct($product,$role){
    //     if(count($product['allowed'])>0){
    //         if(isset($product['allowed'][$role])){
    //             return true;
    //         }  
    //         return false;
    //     }
    //     else{
    //         return true;
    //     }
    // }

    /**
     * list Cards
    */
    public function getCards($customerID /*WC Customer ID*/){
        $customerStripeID = $this->getStripeCustomerID( );
        return $this->stripe->getCards($customerStripeID);
    }

    public function getAllCards(){
        if($this->customerObj){
            //var_dump($this->customerStripeObj);
            var_dump($this->stripe->getCards($this->customerStripeObj['id']));
        }
        //$customerStripeID = $this->getStripeCustomerID( );
        //return $this->stripe->getCards($customerStripeID);
    }

    public function getPaymentMethods($type='card'){
        return $this->stripe->getPaymentMethods(['type'=>$type]);
    }

    public function createSource(){

        $sourceModel = new Source([
            'type' => 'card',
            'currency' => 'usd',
            'owner' => [
                'email' => $this->customerObj->get_email()
            ],
            'usage' => 'reusable',
            'card' => [

            ]
        ]);

        return $this->stripe->getSource('card_1F6Zy9AKZbexXQ0j2dtKp3Xw');

        // if($this->customerObj){
        //     $data = $sourceModel->get();
            
        //     foreach($data as $key => $value){
        //         if($value === ''){
        //             unset($data[$key]);
        //         }
        //     }

        //     return $this->stripe->createSource($data);         
        // }    
        return null;
    }


    /**
     * Get Stripe Customer ID
    */
    private function getStripeCustomerID(){
        if($this->customerObj->meta_exists(ArterosilConfig::instance()->getConfig('CUSTOMER_KEY_REFERENCE'))){
            return $this->customerObj->get_meta(ArterosilConfig::instance()->getConfig('CUSTOMER_KEY_REFERENCE'));    
        }
        return false;  
    }

    /**
     * Retreive PaymentMethod
    */
    function getPaymentMethod($paymentMethodId){
        return $this->stripe->getPaymentMethod($paymentMethodId);
    }

    /**
     * Return Price by Role
    */
    private function getPriceByCustomer($productObj){
        if($this->customerObj){
            $role = $this->customerObj->get_role();
            if($productObj->meta_exists('festiUserRolePrices')){
                $prices = json_decode($productObj->get_meta('festiUserRolePrices'));
                return  $prices->$role;
            }
            else {
                //TODO: change response codes to 400
                return null;
            }
        }
        return null;
    }

    public function SetupIntent(){
        $retData = null;
        if(isset($this->customerStripeObj)){
            $retData = $this->stripe->SetupIntent([
                'stripeID' => $this->customerStripeObj->id
            ]);
        }
        else {
            $retData = $this->stripe->SetupIntent([
                'stripeID' => null
            ]);
        }
        $retData['pk'] = $this->stripe->getPK(); //append stripe Pub Key for client consumption 
        return $retData;
    }

    /**
     * Get Data
    */
    public function getData(){
        $products = $this->getProducts();

        return [
            'customer' => $this->customer->getData(),
            'agent' => $this->agent->getData(),
            'products' => $products
        ];
    }
}