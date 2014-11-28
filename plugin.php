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

define("LAVAPLUGINPATH", dirname(__FILE__));
define("LAVAPLUGINURL", plugin_dir_url( __FILE__ ) );
require_once('lava/class.lava.plugin.core.php');
//logging/error class implementation
require_once "lava/interface/interface.lava.logger.php";
//default class for debugging and error logging
require_once "lava/class.lava.logging.php";
//LavaFactory creates lavaoptions
require_once "lava/class.lava.class.styler.php";
require_once "lava/class.lava.factory.php";
// Load abstract LavaOption class extended by options
require_once "lava/class.lava.plugin.options.php";

/**
 * Class LavaPlugin
 * @uses LavaCorePlugin Version 2.2
 * @package ECT Related Content
 */
class LavaPlugin extends LavaCorePlugin {
	public $prefix = 'lp_';
	public $ver = '1.0.0';
	public $option_prefix = 'lp_';
	public $name = 'lp';
	public $classname;
	public $localize_object = 'LPGLOBAL';
	protected $plugin_slug;
	protected $templates;
	public function init(){
		$this->classname = get_class( $this );
		$this->useFrontendCss = true;
		$this->useFrontendJs = true;
		$this->useAdminCss = true;
		$this->useAdminJs = true;
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", array($this, 'add_settings_page') );
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

$factory = new LavaFactory;

$lavaplugin = new LavaPlugin($factory);