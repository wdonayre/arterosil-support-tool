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

        //include current theme style.css - TEMPORARY COMMENTED
        //echo '<link type="text/css" rel="stylesheet" href="'.get_stylesheet_uri().'" />';
        echo '<link type="text/css" rel="stylesheet" href="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'vendor/twbs/bootstrap/dist/css/bootstrap.min.css"/>';
        echo '<link type="text/css" rel="stylesheet" href="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'vendor/select2/select2/dist/css/select2.min.css"/>';
        echo '<link type="text/css" rel="stylesheet" href="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'includes/order-form/order-form.css"/>';
        //temporary

        echo '<script> var arterosil_ajaxurl = "'.admin_url( 'admin-ajax.php' ).'"; </script>';
        echo '<script> var USERID = "'.$userID.'"; </script>';
        ?>
            <br>
            <div class="container">
                <div class="row">
                    <div class="offset-sm-3 col-sm-6">
                    <form class="form">
                        <!-- customer field -->
                        <!-- <div class="form-group">
                            <label>Customer</label>
                            <select placeholder="Search Customer" class="arterosil-customers form-control" name="arterosil-customers">
                                <option></option>
                            <?php
                                //$users = $this->getUsersByRole('');
                                //foreach($users as $user){
                                //    echo '<option value="'.$user->ID.'">'.$user->display_name.' &lt;'.$user->user_email.'&gt;</option>';
                                //}
                            ?>
                            </select>
                        </div> -->
                        <!-- end : customer field -->



                        <!-- Cart Content Panel -->
                        <div class="form-group">
                            <label>Customer's Cart</label>
                            <div class="card">
                                <div class="card-body product-list  no-items">
                                    <p style="display:none;"> <i class="text-muted">-- Currently no items in cart --</i> </p>
                                    <table class="table product-list-table"></table>
                                    <a class="btn btn-info btn-sm btn-block" href="#" onclick="" data-aaction="addProduct">Add Product</a>
                                    
                                    <!-- Add Product Wrapper -->
                                    <div class="form-row add-product-wrapper a-hidden">
                                        <div class="form-group col-sm-7">
                                            <label>Product</label>
                                            <select class="wc-product-list form-control">
                                                <?php
                                                    $productsReturn = [];
                                                    $params = array(
                                                        'posts_per_page' => -1,
                                                        'post_type' => 'product',
                                                        'post_status' => 'publish'
                                                    );
                                                    $wc_query = new WP_Query($params);

                                                    if ($wc_query->have_posts()){
                                                        //echo json_encode($wc_query->posts);
                                                        echo '<option></option>';
                                                        foreach($wc_query->posts as $product){
                                                            $wc_product = new WC_Product($product->ID);
                                                            $allMeta = get_post_meta($product->ID);
                                                            $allMeta['festiUserRolePrices'] = json_decode($allMeta['festiUserRolePrices'][0]);
                                                            $allMeta['_pricing_rules']="";//json_decode($allMeta['_pricing_rules']);
                                                            array_push($productsReturn, array('meta' => $allMeta) );
                                                            
                                                            echo '<option value="'.$wc_product->get_sku().'">'.get_woocommerce_currency_symbol().$wc_product->get_price()." - ".$product->post_title.'</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>  
                                            <script>
                                                var productsString= ("<?php echo str_replace('"','\"',json_encode($productsReturn)) ?>");

                                            </script>
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
                        
                        <hr>
                        <hr>
                        <small>Temporary Trigger Buttons -- For Development Use Only</small>
                        <br><br>
                        <div class="form-group">
                            <button class="btn btn-secondary btn-sm" data-aaction="createOrder">Create Order </button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-secondary btn-sm" data-aaction="getPaymentGateways">Get Payment Gateways</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
            <br>
            
        <?php
        
        echo '<script type="text/javascript"  src="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'vendor/components/jquery/jquery.min.js"></script>';
        echo '<script type="text/javascript"  src="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'vendor/select2/select2/dist/js/select2.min.js"></script>';
        echo '<script type="text/javascript"  src="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'includes/order-form/order-form.js"></script>';
        

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



