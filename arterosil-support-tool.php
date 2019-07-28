<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/wdonayre
 * @since             1.0.0
 * @package           Arterosil_Support_Tool
 *
 * @wordpress-plugin
 * Plugin Name:       Arterosil Call Support Tool
 * Plugin URI:        #
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            William Donayre Jr.
 * Author URI:        https://github.com/wdonayre
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       arterosil-support-tool
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\Abstracts;


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ARTEROSIL_SUPPORT_TOOL_VERSION', '1.0.0' );

if( ! defined('ARTEROSIL_SUPPORT_TOOL_PLUGIN_FOLDER')) define('ARTEROSIL_SUPPORT_TOOL_PLUGIN_FOLDER', plugin_basename(dirname(__FILE__)));
if( ! defined('ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL')) define('ARTEROSIL_SUPPORT_TOOL_PLUGIN_URL', plugin_dir_url( __FILE__ ));

if( ! defined('ARTEROSIL_SUPPORT_TOOL_PLUGIN_NAME')) define('ARTEROSIL_SUPPORT_TOOL_PLUGIN_NAME', 'arterosil-support-tool');
if( ! defined('ARTEROSIL_SUPPORT_TOOL_PLUGIN_VERSION')) define('ARTEROSIL_SUPPORT_TOOL_PLUGIN_VERSION', '1.0.0');

if( ! defined('ARTEROSIL_CSTOOL')) define('ARTEROSIL_CSTOOL', plugin_dir_path( __FILE__ ).'includes/classes/Arterosil/CSTool/');
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-arterosil-support-tool-activator.php
 */
function activate_arterosil_support_tool() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-arterosil-support-tool-activator.php';
	Arterosil_Support_Tool_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-arterosil-support-tool-deactivator.php
 */
function deactivate_arterosil_support_tool() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-arterosil-support-tool-deactivator.php';
	Arterosil_Support_Tool_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_arterosil_support_tool' );
register_deactivation_hook( __FILE__, 'deactivate_arterosil_support_tool' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-arterosil-support-tool.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function run_arterosil_support_tool() {

	$plugin = new Arterosil_Support_Tool();
	$plugin->run();

}
run_arterosil_support_tool();
