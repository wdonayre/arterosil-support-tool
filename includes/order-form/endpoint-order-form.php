<?php
/*
    Author: William Donayre Jr
    Usage: /order-form
    Description: Generates an order form that processes stripe payment with woocommerce integration
*/

// require __DIR__ . '/../../vendor/autoload.php';
// use Automattic\WooCommerce\Client;
// use Automattic\WooCommerce\Abstracts;

class Arterosil_OrderForm_Endpoint {

    protected $loader;

	/** Hook WordPress
	*	@return void
	*/
	public function __construct(){
        global $plugin;
		add_filter('query_vars', array($this, 'add_query_vars'), 0);
		add_action('parse_request', array($this, 'sniff_requests'), 0);
        add_action('init', array($this, 'add_endpoint'), 0);
    }

	/** Add public query vars
	*	@param array $vars List of current public query vars
	*	@return array $vars
	*/
	public function add_query_vars($vars){
		$vars[] = '__api';
		$vars[] = 'url';
		return $vars;
	}

	/** Add API Endpoint
	*	This is where the magic happens - brush up on your regex skillz
	*	@return void
	*/
	public function add_endpoint(){
		add_rewrite_rule('^order-form/?([0-9]+)?/?','index.php?__api=1&args=$matches[1]','top');
	}

	/**	Sniff Requests
	*	This is where we hijack all API requests
	* 	If $_GET['__api'] is set, we kill WP and serve up pug bomb awesomeness
	*	@return die if API request
	*/
	public function sniff_requests(){
		global $wp;
		if(isset($wp->query_vars['__api'])){
			$this->handle_request();
			exit;
		}
	}

	/** Handle Requests
	*	This is where we send off for an intense pug bomb package
	*	@return void
	*/
    protected function handle_request(){
		global $wp;
        
        if(!isset($_GET['user'])){
            exit;
        } else {
            $userID = $_GET['user'];   
        }
        
        header("Access-Control-Allow-Origin: *");

        //make sure search engines will not index this endpoint
        header("X-Robots-Tag: noindex, nofollow", true);
        
        if ( !is_user_logged_in() ) {
            auth_redirect();
        }
        $agentID = get_current_user_id();

        //include current theme style.css - TEMPORARY COMMENTED
        //echo '<link type="text/css" rel="stylesheet" href="'.get_stylesheet_uri().'" />';
        echo '<link type="text/css" rel="stylesheet" href="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'vendor/twbs/bootstrap/dist/css/bootstrap.min.css"/>';
        echo '<link type="text/css" rel="stylesheet" href="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'vendor/select2/select2/dist/css/select2.min.css"/>';
        echo '<link type="text/css" rel="stylesheet" href="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'includes/order-form/order-form.css"/>';
        //temporary

        echo '<script> var arterosil_ajaxurl = "'.admin_url( 'admin-ajax.php' ).'"; </script>';
        echo '<script> var USERID = "'.$userID.'";  var AGENTID = "'.$agentID.'";</script>';
        ?>
            <div class="order-form loading">
            <div class="circle-loader-outer">
                <div class="circle-loader">
                    <div class="checkmark draw"></div>
                </div>
                <h2 class="order-form-text"></h2>
            </div>
            <br>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" customer">
                            <h5 style="margin:0;" class="card-title customer__name">Customer: <span></span></h5>
                            <p class=" card-text customer__role"><span></span></p>  
                        </div>
                        
                        <hr>
                        <form class="form">
                            <div class="">
                                <div class="row">
                                    <div class="col-sm-8">
                                        
                                        <!-- Cart Content Panel -->
                                        <div class="form-group">
                                            <label>Customer's Cart</label>
                                            <div class="card bg-light rounded-0 border-0">
                                                <div class="card-body product-list  no-items">
                                                    <p style="display:none;"> <i class="text-muted">-- Currently no items in cart --</i> </p>
                                                    <table class="table product-list-table"></table>
                                                    <a class="btn btn-dark btn-md rounded-0" href="#" onclick="" data-aaction="addProduct">Add Product</a>
                                                    
                                                    <!-- Add Product Wrapper -->
                                                    <div class="form-row add-product-wrapper a-hidden">
                                                        <div class="form-group col-sm-7">
                                                            <label>Product</label>
                                                            <select class="wc-product-list form-control">
                                                            </select>  
                                                        </div> 
                                                        <div class="form-group col-sm-2">
                                                            <label>Quantity</label>      
                                                            <input class="form-control" min="1" type="number" name="product-quantity" value="1">      
                                                        </div>
                                                        <div class="form-group col-sm-3">
                                                            <label>&nbsp;</label>
                                                            <button class="btn btn-secondary btn-block" data-aaction="addToCart">Add to cart</button>
                                                        </div>
                                                    </div> <!-- END : Add Product Wrapper -->
                                                </div>
                                            </div>                    
                                        </div> <!-- END : Cart Content Panel -->
                                        
                                        <br><br>
                                        <div class="form-group">
                                            <h4>Summary</h4>
                                            <h6 class="summary__order-total">Order Total: <span></span></h6>
                                            <br>
                                            <button class="rounded-0 btn btn-success btn-lg" data-aaction="processOrder">Process Order</button>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Cards</label>
                                        <div class="card bg-light rounded-0 border-0">
                                            <div class="card-body cards-list product-list">
                                                <div class="form-group">
                                                    <select class="stripe-cards form-control">
                                                    </select>
                                                </div>
                                                <hr>
                                                <div class="add-card-outer">

                                                </div>
                                                <div class="">
                                                    <button class="btn btn-dark btn-block rounded-0" data-aaction="addAnotherCard">Or use another card</button>
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <!-- billing -->
                                        <div class="billing-outer"></div>

                                        <br>
                                        <!-- shipping -->
                                        <div class="shipping-outer"></div>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            <br>
        </div>
        <?php
        echo '<script src="https://js.stripe.com/v3/"></script>'; //load stripe js for card collection

        echo '<script type="text/javascript"  src="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'public/js/handlebars-latest.js"></script>';
        echo '<script type="text/javascript"  src="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'vendor/components/jquery/jquery.min.js"></script>';
        echo '<script type="text/javascript"  src="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'vendor/select2/select2/dist/js/select2.min.js"></script>';

        //Handlebar Templates
        include(dirname(__FILE__).'/templates/hb-templates.php');

        echo '<script type="text/javascript"  src="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'includes/order-form/order-form.js"></script>';
        
        

        ?>

        <?php

    }

    /** Utility Function
	*	Get Users List
	*	@return void
	*/
    protected function getUsersByRole($role){
        $args = array(
            'role' => '',
            'orderby' => 'display_name',
            'order' => 'ASC'
        );

        $users = get_users($args);
        // var_dump($users);
        return $users;
    }
}

new Arterosil_OrderForm_Endpoint();



