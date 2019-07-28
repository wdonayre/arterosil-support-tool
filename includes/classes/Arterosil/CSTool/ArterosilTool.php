<?php

// namespace Arterosil\CSTool;

// use Arterosil\CSTool\ArterosilWoocommerce;
// use Arterosil\CSTool\ArterosilStripe;
require_once plugin_dir_path( dirname( __FILE__ ) ).'CSTool/ArterosilWoocommerce_noneRestAPI.php';
require_once plugin_dir_path( dirname( __FILE__ ) ).'CSTool/ArterosilWoocommerce.php';
require_once (plugin_dir_path( dirname( __FILE__ ) ).'CSTool/ArterosilStripe.php');

class ArterosilTool
{
    private $woocommerce;
    private $stripe;

    private $wooUseRest = true;
    private $stripeUseRest = true;

    public function __construct($wooRest=true,$stripeRest=true){
        //var_dump((plugin_dir_path( dirname( __FILE__ ) ).'CSTool/ArterosilWoocommerce.php'));
        $this->init($wooRest,$stripeRest);
    }

    private function init($wooRest=true,$stripeRest=true){
        /******************************************
         * Initialize woocommerce rest api arterosil class wrapper
        *****************************************/
        $this->woocommerce = new ArterosilWoocommerce();
        $this->woocommerce->init($wooRest);

        /******************************************
         * Initialize Stripe
        *****************************************/
        $this->stripe = new ArterosilStripe();

    }

    public function processPayment($customerID, $sourceID, $orderID){ 
        //get customer object
        $customerObj = json_decode($this->getCustomer($customerID));//$this->woocommerce->get('customers/'.$customerID);
        //get order object
        $orderObj = $this->woocommerce->get('orders/'.$orderID);
        //get source object
        $sourceObj = $this->stripe->getSource($sourceID);// $this->stripe->getSources($customerObj->email,null,false);

        //return array('source'=>$sourceObj, 'customer'=>$customerObj, 'order'=>$orderObj);
        $charge = $this->stripe->createCharge([
            'amount' => intval($orderObj->total)*100,
            'currency' => $orderObj->currency,
            'source'=>$sourceObj->id,
            'description'=>'Arterosil Payment',
            'statement_descriptor' => 'Arterosil Payment',
            'customer'=> $this->getStripeCustomerID($customerObj)
        ]);

        if($charge && $charge->captured){
            //we update woocommerce order status here:
            $data = [
                'status' => 'processing',
                'payment_method' => 'stripe',
                'payment_method_title' => 'Credit Card'
            ];
            return $this->woocommerce->put('orders/'.$orderID,$data);
        } else {
            return "TODO: no charge value need to investigate";
        }
    }

    public function processPaymentNewSource(){
        //TODO - should be able to use striple UI elements in capturing cards
    }

    public function createOrder($orderData){
        //TODO - need to be able to make an order with adjusted pricing by
        if($orderData){
            return $this->woocommerce->post('orders', $orderData);
        }

        return null;
    }
    public function getOrder($orderID){
        $ret = $this->woocommerce->get('orders/'.$orderID);    
        return $ret;
    }
    /**
     * Return customer object from woocommerce
     * (automatically creates stripe customer object when called and if non existing)
    */
    public function getCustomer($customerID){
        //ensure that customer object in wc exist in stripe too
        $customerObj = $this->woocommerce->get('customers/'.$customerID);

        if(!$customerObj) return false; //if no customer id exist

        //prepare data if in case we need to create new customer in stripe 
        $data=[];
        $data['name'] = $customerObj->first_name.' '.$customerObj->last_name;
        $data['email'] = $customerObj->email;
        $data['description'] = ArterosilConfig::instance()->getConfig('STRIPE_CUSTOMER_DESCRIPTION') . " - " .$data['name'];
        
        $customerStripeID = $this->getStripeCustomerID($customerObj);

        if($customerStripeID){
            //verify if customer stripe id exists from stripe server
            $stripeCustomerObj = $this->stripe->getCustomerById($customerStripeID);
            if(!$stripeCustomerObj){
                //create customer stripe object via api
                $stripeCustomerObj = $this->stripe->createCustomer($data);
                $customerObj = $this->setStripeCustomerID($customerID,$stripeCustomerObj->id);
            } 
        } else {
            $stripeCustomerObj = $this->stripe->createCustomer($data);
            $customerObj = $this->setStripeCustomerID($customerID,$stripeCustomerObj->id);
        }
        return json_encode($customerObj);
    }

    /**
     * Get Products
    */
    public function getProducts(){
        return $this->woocommerce->api()->get('products');
    }

    /**
     * list Cards
    */
    public function getCards($customerID /*WC Customer ID*/){
        $customerObj = $this->woocommerce->get('customers/'.$customerID);
        $customerStripeID = $this->getStripeCustomerID( $customerObj );
        return $this->stripe->getCards($customerStripeID);

    }


    /**
     * Get Stripe Customer ID
    */
    private function getStripeCustomerID($customerObj){
        //$customerObj = json_decode($customerObj);
        for($i=0;$i<count($customerObj->meta_data);$i++){
            if($customerObj->meta_data[$i]->key === ArterosilConfig::instance()->getConfig('CUSTOMER_KEY_REFERENCE')){
                return $customerObj->meta_data[$i]->value;
            }
        }
        return false;  
    }

    /**
     * PRIVATE METHODS
    */

    private function setStripeCustomerID($customerID, $stripeID){
        $data = [
            'meta_data' => [
                [
                    'key' => ArterosilConfig::instance()->getConfig('CUSTOMER_KEY_REFERENCE'),
                    'value' => $stripeID
                ]
            ]
        ];
        return $this->woocommerce->put('customers/'.$customerID,$data);
    }
}