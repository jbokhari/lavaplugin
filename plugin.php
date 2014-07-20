<?php
/**
 * Plugin Name: Lava Plugin
 * Plugin URI: http://www.anchorwave.com
 * Description: Used to create featured post UI page.
 * Version: 1.0.0
 * Author: Jameel Bokhari
 * Author URI: http://www.anchorwave.com
 * License: GPL2
 */
/*
Copyright 2013  Jameel Bokhari  ( email : me@jameelbokhari.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/**
 * Dear future developers (poor souls):
 * To add or edit options and fields see lava/settings.php where the values are used to create fields in the settings page. The LavaCorePlugin class sets up all the jibba-jabba to create an options page and store them in the data base as post meta.
 * Functionality for this plugin is aliased through rc_related_content(), which is definied in library/functions.php
 * The aformentioned function uses the file related-content.php, which is where the bulk of the specific-to-this-plugin functionallity can be found.
 * Also noteworthy, this plugin utilizes three other plugins (although has fallbacks if they are not installed). These plugins include Advanced Custom Fields v 4.3.2, Repeater Field v 1.1.1 and wp-days-ago v 3.0.3. These are the current versions, it should also work with future version or fallback to secodary options but don't quote me on that.
 * Last Updated July 15, 2014 
 */
define("ECT_RELATED_CONTENT_PATH", dirname(__FILE__));
define("ECT_RELATED_CONTENT_URL", plugin_dir_url( __FILE__ ) );
require_once('lava/class.lava.plugin.core.php');
/**
 * Class LavaPlugin
 * @uses LavaCorePlugin Version 2.2
 * @package ECT Related Content
 */
class LavaPlugin extends LavaCorePlugin22 {
	public $prefix = 'rc_';
	public $ver = '1.0.0';
	public $option_prefix = 'rc_';
	public $name = 'rc';
	public $localize_object = 'RC';
	protected $plugin_slug;
	protected static $instance;
	protected $templates;
	public function __construct(){
		parent::__construct();
		$this->init();
	}
	public function init(){
		$this->useFrontendCss = true;
		$this->useFrontendJs = true;
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", array($this, 'add_settings_page') );
	}
	
	public static function get_instance() {
		if (null == self::$instance ) {
			self::$instance = new LavaPlugin();
		}
		return self::$instance;
	}
	function option($option, $default = null){
		echo $this->get_option($option, $default);
	}
	function get_option($option, $default = null){
		return $this->get_cache($option, $default);
	}
	/**
	 * Overrides default functionality
	 * @return type
	 */
	function admin_enqueue_scripts_and_styles(){
		$version = $this->get_script_version();
		if ( $this->useFrontendCss ){
			wp_enqueue_style( 'related-content-admincss', $this->cssdir . 'admin.css', array(), $version, $media = 'all' );
		}

		if ( $this->useFrontendJs ){
			wp_register_script( 'related-content-adminjs', $this->jsdir . 'admin.js', 'jquery', $version );

			$js_global = $this->get_localized_js_object_name();
			$adminJSVars = $this->set_frontend_loc_js_values();
			apply_filters( "related-content-admin-js-vars", $adminJSVars );
			wp_localize_script( 
				'related-content-adminjs',
				$js_global,
				$adminJSVars
			);

			wp_enqueue_script('related-content-adminjs');
		}
		wp_enqueue_script( 'suggest' );
		wp_enqueue_script( 'autocomplete', $this->jsdir . 'jquery-ui-autocomplete.min.js', array('jquery'), $version );	
	}
	function add_settings_page($links) { 
	  $settings_link = '<a href="'.$this->static['options_page']['parent_slug'].'?page='.$this->static['options_page']['menu_slug'].'">Settings</a>'; 
	  array_unshift($links, $settings_link); 
	  return $links; 
	}
	 
	


}
// $LavaPlugin = new LavaPlugin();
$LavaPlugin = LavaPlugin::get_instance();
