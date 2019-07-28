<?php



use Automattic\WooCommerce\Client;
use Stripe\Stripe;
use Stripe\Balance;
use Stripe\Customer;
use Stripe\Charge;

class ArterosilStripe
{
    private $currentCustomer = null;

    public function __construct(){
        $this->init();
    }

    private function init(){
        Stripe::setApiKey(ArterosilConfig::instance()->getConfig('STRIPE_SECRET_KEY'));
    }

    public function processPayment(){ //#1

    }

    public function getBalance(){
        return Balance::retrieve();
    }
    
    /**
     * Get Customer object
    */
    public function getCustomer($email, $info=null, $create=true){
        if(isset($email)){
            $customer = Customer::all(["limit"=>1,"email"=>$email]);

            $this->currentCustomer = $customer; //assign current customer 

            if(count($customer->data)>0){
                return $customer->data[0];
            }
            else{
                //we will create the customer object
                if(isset($info) && $create){
                    $info['email'] = $email;
                    $info['description'] = ArterosilConfig::instance()->getConfig('STRIPE_CUSTOMER_DESCRIPTION') . " - " .$info['name'];
                    $customer = Customer::create($info);
                }
                return $customer;
            }
        }
        return null;
    }

    /**
     * Get Customer object by stripe id
    */
    public function getCustomerById($customerStripeID, $info=null, $create=false){
        if(isset($customerStripeID)){
            try{
                $customer = Customer::retrieve($customerStripeID);
                if($customer){
                    $this->currentCustomer = $customer; //assign current customer 
                    return $customer;
                } else return false;
            } catch (\Exception $e){
                return false;
            }
        }
        return null;
    }

    /**
     * Add Customer to stripe
    */
    public function createCustomer($info){
        if($info){
            $customer = Customer::create($info);
        }
        return $customer;
    }

    /**
     * Retrieves cached Customer object
    */
    public function currentCustomer(){
        return $this->currentCustomer;
    }

    /**
     * Clears cached Customer object
    */
    public function clearCurrentCustomer(){
        $this->currentCustomer = null;
    }

    /**
     * Gets Customer source (e.g. card, bank etc..)
    */
    public function getSource($sourceID){
        //var_dump($this->currentCustomer);
        if($this->currentCustomer && $sourceID){
            try{
                return $this->currentCustomer->sources->retrieve($sourceID);
            } catch(\Exception $e){
                return array('error'=>true, 'message'=>'source id doesn\'t exist');
            }
        }
        return null;
    }

    /**
     * Gets Customer source(s) (e.g. card, bank etc..)
    */
    public function getSources($email,$info,$create){
        if( $this->currentCustomer->email && $this->currentCustomer->email === $email ){
            return $this->currentCustomer->sources->data;
        }
        else {
            $customerObj = $this->getCustomer($email,$info,$create);
            if($customerObj){
                return $customerObj->sources->data;
            } else {
                return null;
            }
        }
    }

    public function createCharge($data){
        return Charge::create($data);   
    }

    public function getCards($customerID,$limit=3){
        return Customer::allSources(
            $customerID,[
                'limit' => $limit,
                'object'=>'card'
            ]
        );
    }
}