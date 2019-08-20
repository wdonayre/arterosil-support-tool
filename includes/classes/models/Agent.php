<?php 

namespace ZendeskCSWooCart\Models;

class Agent extends User{

    public function __construct(){
        $userID = get_current_user_id();
        parent::__construct($userID,false);
    }
}