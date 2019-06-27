<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/wdonayre
 * @since      1.0.0
 *
 * @package    Arterosil_Support_Tool
 * @subpackage Arterosil_Support_Tool/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Arterosil_Support_Tool
 * @subpackage Arterosil_Support_Tool/includes
 * @author     William Donayre Jr. <william@phillipswdc.com>
 */
class Arterosil_Support_Tool_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'arterosil-support-tool',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
