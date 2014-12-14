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
require_once "lava/interface/interface.lava.notifier.php";
//default class for debugging and error logging
require_once "lava/class.lava.notifier.php";
require_once "lava/class.lava.logging.php";
//LavaFactory creates lavaoptions
require_once "lava/class.lava.factory.php";
// Load abstract LavaOption class extended by options
require_once "lava/class.lava.plugin.options.php";

/**
 * Class LavaPlugin
 * @uses LavaCorePlugin Version 2.2
 * @package ECT Related Content
 */
class LavaPlugin extends LavaCorePlugin {
	static $prefix = 'lp_';
	public $ver = '1.0.0';
	public $option_prefix = 'lp_';
	static $name = 'LavaPlugin';
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
	function add_settings_page($links) { 
	  $settings_link = '<a href="'.$this->static['options_page']['parent_slug'].'?page='.$this->static['options_page']['menu_slug'].'">Settings</a>'; 
	  array_unshift($links, $settings_link); 
	  return $links; 
	}
}
$optionfactory = new LavaFactory();
$loggingobject = new LavaLogging( LavaPlugin::$name );
$notifierobject = new LavaNotifier( LavaPlugin::$prefix );
$lavaplugin = new LavaPlugin($optionfactory, $loggingobject, $notifierobject);