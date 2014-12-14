<?php
/**
 * Plugin core defines several methods for Lava Plugins
 * @package Lava
 * @version 2.2
 * @author Jameel Bokhari
 * @license GPL2
 * Last updated 7/20/2014
 */
if (!class_exists('LavaCorePlugin')) :

	class LavaCorePlugin {
		public $optionspage = array();
		public $options = array();
		public $cache = array();
		public $useAdminCss = false;
		public $useAdminJs = false;
		public $useFrontendCss = false;
		public $useFrontendJs = false;
		public $version = "0.0.0";
		public $newStatic;
		public $newDynamic;
		public $tabs;
		public $dir;
		public $jsdir;
		public $cssdir;
		public $admin_message = "";
		private static $queued_si_scripts = array();
		private static $fieldnumber = 0;

		protected $option_prefix;
		protected $ver;

		public function __construct($optionfactory = null, $loggingobject = null, $notifierobject = null){

			$this->factory = $optionfactory ? $optionfactory : null;
			$this->logger = $loggingobject ? $loggingobject : null;
			$this->notifier = $notifierobject ? $notifierobject : null;

			$this->set_options($options);
			$this->dir = plugin_dir_url( __FILE__ );
			$this->cssdir = $this->dir . '../library/css/';
			$this->jsdir = $this->dir . '../library/js/';

			register_activation_hook( __FILE__, array($this, 'plugin_activate') );
			register_deactivation_hook( __FILE__, 'plugin_deactivate' );

			add_action( 'admin_menu', array($this, 'add_admin_page_to_menu') );	
			$this->enqueue_scripts();
			add_action( 'admin_head', array($this, "save_admin"));
			$this->init();
		}
		public static function addsiscript($param){
			vardump($param);
		}
		final static function get_dir(){
			return plugin_dir_url( __FILE__ );
		}
		final static function get_js_dir(){
			return plugin_dir_url( __FILE__ ) . "../library/js/";
		}
		final static function get_css_dir(){
			return plugin_dir_url( __FILE__ ) . "../library/css/";
		}
		public function enqueue_scripts(){
			add_action( 'wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts_and_styles'), 999);

			add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_and_styles'), 100 );
		}
		public function init(){
			return;
		}

		public function prefix($option){
			$option = $this->option_prefix . $option;
			return $option;
		}

		public function set_options(){
			require_once( plugin_dir_path(__FILE__) . '/../settings.php' );
			$this->static = $static;
			foreach( $dynamic as $option ) {
				$name = $option['name'];
				$this->options[$name] = $this->factory->create($this->prefix, $option, $this->classname );
			}
		}
		/** TODO: Broken! These need to be rewritten to follow best practices for de/activation **/ 
		public function plugin_activate(){
			foreach( $this->dynamic as $name => $values){
				$option = $this->prefix($name);
				add_option( $option, $values['default'] );
			}
		}
		public function plugin_deactivate(){
			foreach( $this->dynamic as $name => $values){
				$option = $this->prefix($name);
				delete_option( $option );
			}
		}
		public function do_tabs($current){
		    echo '<div id="icon-themes" class="icon32"><br /></div>';
		    echo '<h2 class="nav-tab-wrapper">';
			$plugin_tabs = $this->static['tabs'];
		    $tabindex = 0;
		    foreach( $plugin_tabs as $tabslug => $values ){
		    	$label = $values['label'];
		        $class = ( $tabindex == $current ) ? 'nav-tab-active' : '';
		        echo "<a class='nav-tab $class' href='options-general.php?page=plugin-options&tab=$tabindex'>$label</a>";
		        $tabindex++;
		    }
		    echo '</h2>';
		}
		public function set_frontend_loc_js_values(){
			$include = array();
			$include['plugin_prefix'] = $this->option_prefix;
			$include['plugin_version'] = $this->version;

			foreach($this->options as $option){

				if ( isset( $option->in_js ) && $option->in_js == true ){
					$value = $option->get_value();
					$include[$name] = $value;
				}

			}	
			return $include;
		}
		/**
		 * Gets the current version of the plugin. If the plugin has an option named debug and if it's set to anything that returns true, the plugin will generate a rand unique ID for version. This can be used to always refresh scripts.
		 * @return (string) plugin verion or random string
		 */
		public function get_script_version(){
			$option = $this->prefix('debug');
			if (get_option( $option, $default = false )){
				return uniqid();
			} else {
				return $this->ver;
			}
		}
		/**
		 * Loops through each registered option
		 * @param (int) $current_tab 
		 * @return type
		 */
		public function update_admin_options($current_tab = null){
			$msg = '';
			$affected = 0;
			extract($_POST);
			print_r($_POST);
			foreach ($this->options as $option) {
				$name = $option->name;
				$this->logger->_log("Inside loop to save $option->name...");
				if ( $option->type == 'info' ) {
					$this->logger->_log("{$this->name} was a info field, skipping save function.");
					continue;
				}
				if ($option->tab != $current_tab){
					$this->logger->_log("{$this->name} is not member of current tab $current_tab, skipping save function.");
					continue;
				}
				if ( $option->in_menu == false ){
					$this->logger->_log("{$this->name} is not in_menu, skipping save function.");
				}
				// $this->logger->_log(print_r($option, true));
				$this->logger->_log("Current Tab: $current_tab");
				// rather than check if the value is bool, we'll just assume that when we get this far, if the post data is missing, the value is false;
				$newValue = isset($$name) ? $$name : "";
				if ( $option->in_menu ){
					// $this->logger->_log("set_value is being run. {$newValue}");
					if ($option->set_value($newValue) )
						$affected++;
				}
			}
			if( $affected > 0 ){
				$this->notifier->add( "Options have been saved.", "updated" );
			} else {
				var_dump($this->notifier);
				$this->notifier->add( "No options were changed!", "notice" );
			}
		}
		/**
		 * Determines whether or not to save plugin settings. Checks if save_post variable is set by hidden field in this plugin, verifies nonce and user permission. If all passes, runs update_admin_options()
		 * @uses self::update_admin_options()
		 * @return (string) A message based on actions performed (or not performed).
		 */
		public function save_admin(){
			if ( get_current_screen() == $this->settings_page ){

				$savepost = $this->prefix . "save_post";
				if( isset( $_POST[$savepost] ) ) :
					$noncename = $this->prefix . 'nonce';
					$nonceaction = $this->prefix . 'do_save_nonce';
					$nonce = ( isset( $_POST[$noncename] ) ) ? $_POST[$noncename] : '' ;
					$current_tab = ( isset( $_POST['tab'] ) ? $_POST['tab'] : null );
					if ( wp_verify_nonce( $nonce, $nonceaction ) ){
						$this->logger->_log("Nonce verified for saving options.");
					 	if ( current_user_can( 'manage_options' ) ){
					 		$this->logger->_log("User verified for saving options.");
							$this->update_admin_options($current_tab);
						} else {
							$this->notifier->add( "You lack permission to modify these settings.", "error" );
						}
					} else {
						$this->notifier->add( "There was an error with the nonce field. Please try again.", "error" );
					}
				endif;
			}
		}
		/**
		 * Display admin page by printing html to page.
		 * @return void
		 */
		public function display_admin_page(){
			echo "<div class='wrap " . $this->prefix . "options-page " . $this->prefix . "wrap'>";
			// $msg = $this->save_admin();
			$current_tab = ( isset( $_GET['tab'] ) ) ? intval( $_GET['tab'] ) : 0 ;
			$msg = '';
			$noncename = $this->prefix . 'nonce';
			$nonceaction = $this->prefix . 'do_save_nonce';
			// $msg = $this->save_admin();
			echo "<h2 class='" . $this->prefix . "option-page-title'>Plugin Options</h2>";

			$this->do_tabs($current_tab);

			echo "<form action='' method='post'>";
			$this->generate_option_fields($current_tab);
			$key = intval($current_tab);
			$tabvals = $this->static['tabs'][$key];
			$hidesave = ( isset($tabvals['informational']) ) ? $tabvals['informational'] : false;
			if ( ! $hidesave ){
				$savepost = $this->prefix . "save_post";
				echo "<input type='hidden' name='{$savepost}' value='1' />";
				echo "<input type='hidden' name='tab' value='{$current_tab}' />";
				wp_nonce_field( $nonceaction, $noncename );
				echo "<button class='button button-primary {$this->prefix}plugin-save-btn' type='submit'>Save Options</button>";
			}
			$this->debug_info();
			echo "</form>";
			echo "</div><!-- EOF WRAP -->";
		}
		public function debug_info(){
			// return false;
			//Used to do all this...
			// if( $this->is_debug_mode() ){ // no longer defined
			// 	// print_r($this->cache);
				$this->logger->display_errors();
			// 	$this->display_logs();
				foreach($this->options as $option){
			// 		echo $option->label;
					$option->logger->display_logs();
					$option->logger->display_errors();
			// 	}
			}
		}
		public function generate_option_fields($tab){
			foreach ($this->options as $option) {
				if( $option->tab != $tab ){
					continue;
				}
				// $this->fieldnumber++;
				// @uses static int $this->fieldnumber starting at 1
				if ( isset( $option->in_menu ) && $option->in_menu ){
					echo $option->get_option_header_html();
					echo $option->get_option_label_html();
					echo $option->get_option_field_html();
					echo $option->get_option_footer_html();
				
				}
			}
		}
		/**
		 * Register the admin page
		 * @return type
		 */
		public function add_admin_page_to_menu(){
			wp_enqueue_media();
			$function = array($this, 'display_admin_page');
			$admin_page = $this->static['options_page'];
			$this->settings_page = add_submenu_page( $admin_page['parent_slug'], $admin_page['page_title'], $admin_page['menu_title'], $admin_page['capability'], $admin_page['menu_slug'], $function );
		}
		/**
		 * Creates information field that takes no input
		 * @param type $label 
		 * @param type $id 
		 * @param type $value 
		 * @param type $description 
		 * @return string
		 */
		public function info_field($label, $id, $value, $description){
			$html = "";
			if ( $label != '' ){
				$label = "<h2 id='{$id}'>$label</h2>";
			}
			$html .= $label;
			$html .= "<p>$value</p>";
			$html .= "<p class='description'>$description</p>";

			return $html;
		}
		public function frontend_enqueue_scripts_and_styles(){
			$version = $this->get_script_version();
			if ( $this->useFrontendCss ){
				wp_enqueue_style( 'lavafrontendcss', $this->cssdir . 'css/frontend.css', array(), $version, $media = 'all' );
			}

			if ( $this->useFrontendJs ){
				wp_register_script( 'lavafrontendjs', $this->jsdir . 'frontend.js', 'jquery', $version );

				$js_global = $this->get_localized_js_object_name();

				wp_localize_script( 
					'lavafrontendjs',
					$js_global,
					$this->set_frontend_loc_js_values()
				);

				wp_enqueue_script('lavafrontendjs');
			}
		}
		public function get_localized_js_object_name(){
			if ( !empty($this->prefix) ){
				return $this->localize_object;
			} else if ( !empty($this->prefix) ){
				return strtoupper($this->prefix);
			} else if ( !empty($this->name) ){
				return strtoupper($this->prefix);
			} else {
				return "LAVAOBJ";
			}
		}
		public function admin_enqueue_scripts_and_styles(){
			$version = $this->get_script_version();
			$name = $this->name;
			if ( $this->useAdminCss ){
				wp_enqueue_style( 'lavaadmincss', $this->cssdir . 'admin.css', array(), $version, $media = 'all' );
			}
			if ( $this->useAdminJs ){
				wp_register_script( 'lavaadminjs', $this->jsdir . 'admin.js', 'jquery', $version );
				// $js_global = $this->get_localized_js_object_name();
				wp_enqueue_script('lavaadminjs');
			}
		}

	}/* EOF Class LavaOptions */
endif;

/*EOF*/