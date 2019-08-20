<?php 

class Source
{
    private $type;
    private $amount;
    private $currency;
    private $flow;
    private $mandate;
    private $metadata;
    private $owner;
    private $receiver;
    private $redirect;
    private $source_order;
    private $statement_descriptor;
    private $token;
    private $usage;
    private $properties;

    public function __construct($args){
        
        $this->properties = [
            'type' => '',
            'currency' => '',
            'flow' => '',
            'mandate' => '',
            'metadata' => '',
            'owner' => [
                'address' => '',
                'email' => '',
                'name' => '',
                'phone' => ''
            ],
            'receiver' => '',
            'redirect' => '',
            'source_order' => '',
            'statement_descriptor' => '',
            'token' => '',
            'usage' => ''
        ];

        //check required
        if(!isset($args['type'])){
            throw new Exception("'type' is required");    
            exit();
        }
        else{
            $this->properties['type'] = $args['type'];
        }

        if(isset($args['amount']))      $this->properties['type']['amount'] = $args['amount'];
        if(isset($args['currency']))    $this->properties['currency']= $args['currency'];
        if(isset($args['flow']))        $this->properties['flow'] = $args['flow'];
        if(isset($args['mandate']))     $this->properties['mandate'] = $args['mandate'];
        if(isset($args['metadata']))    $this->properties['metadata'] = $args['metadata'];
        if(isset($args['owner'])){
            $this->properties['owner'] = $args['owner'];
        }
        if(isset($args['receiver']))                    $this->properties['receiver'] = $args['receiver'];
        if(isset($args['redirect']))                    $this->properties['redirect'] = $args['redirect'];
        if(isset($args['source_order']))                $this->properties['source_order'] = $args['source_order'];
        if(isset($args['statement_descriptor']))        $this->properties['statement_descriptor'] = $args['statement_descriptor'];
        if(isset($args['token']))                       $this->properties['token'] = $args['token'];
        if(isset($args['usage']))                       $this->properties['usage'] = $args['usage'];
    }

    public function get($property=null){
        if(isset($property)){
            return $this->properties[$property];
        }
        return $this->properties;
    }

    public function set($property=null,$value=''){
        if(isset($property)){
            if(isset($this->properties[$property])){
                $this->properties[$property] = $value;
            }
            return true;
        }
        return false;
    }

}