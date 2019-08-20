<?php
/*
    Author: William Donayre Jr
    Usage: /order-form
    Description: Generates an order form that processes stripe payment with woocommerce integration
*/

class Arterosil_OrderForm {

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

        //make sure search engines will not index this endpoint
        header("X-Robots-Tag: noindex, nofollow", true);

        //include current theme style.css - TEMPORARY COMMENTED
        //echo '<link type="text/css" rel="stylesheet" href="'.get_stylesheet_uri().'" />';
        echo '<link type="text/css" rel="stylesheet" href="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'vendor/twbs/bootstrap/dist/css/bootstrap.min.css"/>';
        echo '<link type="text/css" rel="stylesheet" href="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'vendor/select2/select2/dist/css/select2.min.css"/>';
        //temporary
        
        ?>
            <br>
            <div class="container">
                <div class="row">
                    <div class="offset-sm-3 col-sm-6">
                    <form class="form">
                        <div class="form-group">
                            <select class="arterosil-customers form-control" name="arterosil-customers">
                            <?php
                                $users = $this->getUsersByRole('');
                                foreach($users as $user){
                                    echo '<option value="'.$user->ID.'">'.$user->display_name.'</option>';
                                }
                            ?>
                            </select>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
            <br>
            
        <?php

        echo '<script type="text/javascript"  src="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'vendor/components/jquery/jquery.min.js"></script>';
        echo '<script type="text/javascript"  src="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'vendor/select2/select2/dist/js/select2.min.js"></script>';
        echo '<script type="text/javascript"  src="'.ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL.'public/js/ep-order-form.js"></script>';
        

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
// Instantiate Endpoint
new Arterosil_OrderForm();
