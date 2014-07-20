<?php
/**
 * Plugin core defines several methods for Lava,
 * @package Lava
 * @version 2.2
 * @author Jameel Bokhari
 * @license GPL2
 * Last updated 7/17/2014
 */
if (!class_exists('LavaCorePlugin22')) :
	require_once "class.lava.logging.php";
	require_once "class.lava.plugin.options.php";
	class LavaCorePlugin22 extends LavaLogging22 {
		public $optionspage = array();
		public $options = array();
		public $cache = array();
		public $useAdminCss = false;
		public $useAdminJs = false;
		public $useFrontendCss = false;
		public $useFrontendJs = false;
		public $newStatic;
		public $newDynamic;
		public $tabs;
		public $name;
		protected static $fieldnumber = 0;

		protected $option_prefix;
		protected $ver;

		public function __construct($options = null, $prefix = 'lava_'){
			$this->set_options($options);
			$dir = plugin_dir_url( __FILE__ );
			$this->cssdir = $dir . '../library/css/';
			$this->jsdir = $dir . '../library/js/';

			register_activation_hook( __FILE__, array($this, 'plugin_activate') );
			register_deactivation_hook( __FILE__, 'plugin_deactivate' );

			add_action( 'admin_menu', array($this, 'add_admin_page_to_menu') );	
			$this->init();
			$this->enqueue_scripts();
		}
		public function enqueue_scripts(){
			if( $this->get_cache( 'plugin_activated', false ) ){
				add_action( 'wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts_and_styles'), 999);

			}
			add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_and_styles'), 100 );
		}
		public function init(){
			return;
		}

		public function get_cache($option, $default = null){
			// if ( isset( $_GET['test'] ) ) echo "$option";
			if ( !empty( $this->cache[$option] ) ){
				return $this->cache[$option];
				$this->_log("Option was found in cache for $option");
			} else {
				$this->_log("No option found in cache, creating cached value for $option");
				$id = $this->prefix($option);
				if ($default === null){
					$default = $this->options[$option]['default'];
				}
				$this->cache[$option] = get_option($id, $default);
				// if ( isset( $_GET['test'] ) ) print_r($this->cache[$option]);
				// if ( isset( $_GET['test'] ) ) echo $id;
				return $this->cache[$option];
			}

		}
		protected function prefix($option){
			$option = $this->option_prefix . $option;
			return $option;
		}

		public function set_options($options = null){
			//settings and options
			//settings are for plugin, ie tabs menus etc
			//options are for what user chooses, plugin settings
			if ($options === null){
				$path = '';
				require_once( plugin_dir_path(__FILE__) . '/../settings.php' );
				$this->static = $static;
				$this->dynamic = $dynamic;
				foreach( $this->dynamic as $option ) {
					$name = $option['name'];
					$this->newDynamic[$name] = LavaFactory::create($this->prefix, $option );
				}
			} else {
				$this->static = $options['static'];
				$this->dynamic = $options['dynamic'];
			}
		}
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
		public function update_option( $name, $value ){
			$this->_log( __FUNCTION__ . " was executed using $name and $value");
			$option = $this->dynamic[$name];

			if( $value == '' && isset( $option['required'] ) && $option['required'] ){
			//is it required?
				//try old value
				$this->log("$this->dynamic['name'] was reverted to its old state because it was required but the value was empty.");
				$value = $option['default'];
				return update_option($option, $value );
			}
			switch($option['type']) {
				case 'url':
				case 'email':
				case 'str':
				case 'textarea':
					$value = esc_textarea( $value );
					break;
				case 'int' :
					$value = intval($value);
					break;
				case 'bool' :
					$value = $value;
					break;
				case 'image' :
					$value = $value;
					break;
				case 'array' :
					// if value is an available option
					echo $value;
					$choices = array();
					foreach($option['values'] as $key => $l ){
						$choices[] = $key;
					}
					$value = ( in_array( $value, $choices ) ) ? $value : $option['default'];
					// $value = $value;
					break;
				default :
					$value = $value;
					break;
			}
			$id = $this->option_prefix . $name;
			return update_option($id, $value );
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
		public function set_admin_loc_js_values(){
			$include = array();
			$include['prefix'] = $this->option_prefix;

			foreach($this->dynamic as $name => $values){

				if ( isset($values['in_admin_js']) ){
					$default = $values['default'];
					$value = $this->get_cache( $name, $default );
					$include[$name] = $value;
				}

			}	
			return $include;
		}
		public function set_frontend_loc_js_values(){
			$include = array();
			$include['prefix'] = $this->option_prefix;

			foreach($this->dynamic as $name => $values){

				if ( isset($values['in_js']) ){
					$default = $values['default'];
					$value = $this->get_cache( $name, $default );
					$include[$name] = $value;
				}

			}	
			return $include;
		}
		public function get_script_version(){
			$option = $this->prefix('debug');
			if (get_option( $option, $default = false )){
				return uniqid();
			} else {
				return $this->ver;
			}
		}
		public function update_admin_options($current_tab = null){
			$msg = '';
			$affected = 0;
			extract($_POST);
			// print_r($_POST);
			// print_r($this->cache);
			foreach ($this->dynamic as $name => $values) {
				if ( $values['type'] == 'info' ) {
					continue;
				}
				if ( $values['in_menu'] && $values['tab'] == $current_tab ){
					if (!isset($$name) && $values['type'] == 'bool'){
						$newvalue = false;
					} else {
						$newvalue = $$name;
					}
					if ( $this->update_option($name, $newvalue) ){
						$this->cache[$name] = $newvalue;
						$affected++;
						// $this->
					}
				}

			}
			if( $affected > 0 ){
				$msg .= "Options have been saved.";
			} else {
				$msg .= 'No options were changed!';
			}

			return $msg;
		}
		public function display_admin_page(){

			echo "<pre>";
			print_r($this);
			echo "</pre>";

			global $plugin_tabs;
			$screen = get_current_screen();
			// print_r($screen);
			echo "<div class='wrap " . $this->prefix. "options-page " . $this->prefix . "wrap'>";

			$current_tab = ( isset( $_GET['tab'] ) ) ? intval( $_GET['tab'] ) : 0 ;
			$msg = '';
			$noncename = $this->prefix . 'nonce';
			$nonce = ( isset( $_POST[$noncename] ) ) ? $_POST[$noncename] : '' ;
			$nonceaction = $this->prefix . 'do_save_nonce';
			if( isset($_POST['save_post']) ){
				if ( wp_verify_nonce( $nonce, $nonceaction ) && current_user_can( 'manage_options' ) ){
					$msg = $this->update_admin_options($current_tab);
				} else {
					wp_die("You lack permission to modify these settings.");
				}
				
			}

			echo "<h2 class='" . $this->prefix. "option-page-title'>Plugin Options</h2>";

			$this->do_tabs($current_tab);

			if($msg != ''){
				echo "<div id='message " . $this->prefix. "message' class='updated'><p>{$msg}</p></div>";
			}

			echo "<form action='' method='post'>";
			$options = $this->dynamic;
			foreach ($options as $name => $values) {
				if( isset( $values['tab'] ) && $values['tab'] != $current_tab ){
					continue;
				}
				$this->fieldnumber++;
				// @uses static int $this->fieldnumber starting at 1
				echo "<div class='option-block field-{$this->fieldnumber}'>";
				if ( isset( $values['in_menu'] ) && $values['in_menu'] ){
					$this->display_admin_option($name);
				}
				echo "<div style='clear:both;'></div>";
				echo "</div>";
			}
			$key = intval($current_tab);
			$tabvals = $plugin_tabs[$key];
			$hidesave = ( isset($tabvals['informational']) ) ? $tabvals['informational'] : false;
			if ( ! $hidesave ){
				echo "<input type='hidden' name='save_post' value='1' />";
				echo "<input type='hidden' name='tab' value='{$current_tab}' />";
				wp_nonce_field( $nonceaction, $noncename );
				echo "<button class='button button-primary blank_plugin-save-btn' type='submit'>Save Options</button>";
			}
			if( $this->is_debug_mode() ){
				// print_r($this->cache);
				$this->display_errors();
				$this->display_logs();
			}
			echo "</form>";
			echo "</div><!-- EOF WRAP -->";

		}
		public function is_debug_mode(){
			return $this->get_cache( 'debug', $default = false );
		}
		/**
		 * Register the admin page
		 * @return type
		 */
		public function add_admin_page_to_menu(){
			wp_enqueue_media();
			$function = array($this, 'display_admin_page');
			$admin_page = $this->static['options_page'];
			add_submenu_page( $admin_page['parent_slug'], $admin_page['page_title'], $admin_page['menu_title'], $admin_page['capability'], $admin_page['menu_slug'], array($this, 'display_admin_page' ) );
		} 
		public function display_admin_option( $name ){
			// print_r($this);
			$option = $this->dynamic[$name];
			$type = $option['type'];
			$default = $option['default'];
			if ($type === 'function'){
				if ( method_exists($this, $default) ){
					return $this->$default();
				}
			}

			$html = '';

			// strval may seem a little paranoid but for extension may be necessary
			$admin_before = ( isset($option['admin_before'])) ? strval($option['admin_before']) : '';
			$admin_after = ( isset($option['admin_after'])) ? strval($option['admin_after']) : '';
			$class = ( isset($option['class'])) ? strval($option['class']) : '';
			$description = ( isset($option['description'])) ? strval($option['description']) : '';
			$after = ( isset($option['after'])) ? strval($option['after']) : '';
			// $inputClass = ( isset($option['inputClass'])) ? strval($option['inputClass']) : '';

			if ( isset( $option['label'] ) ){
				$label = $option['label'];
			} else {
				$label = '';
			}
			// attributes value, name, type, required
			$id = $this->prefix($name);
			$value = $this->get_cache($name, $default );
			// $nameattr = $name; not necessary but good reminder
			$required = ( isset($option['required']) && $option['required'] == true ) ? 'required="required"' : '' ;
			if ( isset( $option['values'] ) ){
				$valuesarray = $option['values'];
			}
			$html .= $admin_before;
			if ($type == 'info'){
				$html .= $this->info_field($label, $id, $default, $description);
				echo $html;
				return true;
			}
			$html .= $after;		
			$html .= "<label class='option-label' for='{$id}'>$label</label>";
			$value = stripslashes( $value );
			switch($type){
				case 'sortable':
					$html = $this->create_sortable($name, $option);
					break;
				case 'url':
					$html .= "<input id='{$id}' class='' {$required} type='url' name='{$name}' value='{$value}' />";
					break;
				case 'email':
					$html .= "<input id='{$id}' class='{$class}' {$required} type='email' name='{$name}' value='{$value}' />";
					break;
				case 'str':
					$html .= "<input id='{$id}' class='{$class}' {$required} type='text' name='{$name}' value='{$value}' />";
					break;
				case 'textarea':
					$html .= "<textarea id='{$id}' class='{$class}' {$required} type='text' name='{$name}' >{$value}</textarea>";
					break;
				case 'int' :
					$html .= "<input id='{$id}' class='{$class}' {$required} type='number' min='' max='' name='{$name}' value='{$value}' />";
					break;
				case 'bool' :
					$checked = ($value) ? 'checked="checked"' : '' ;
					echo $value;
					$html .= "<input id='{$id}' class='{$class}' {$required} {$checked} type='checkbox' name='{$name}' value='1' />";
					break;
				case 'image' :
					$html .= "<div class='image-container'>
						<img id='{$id}_preview' class='image-preview {$id}-preview' src='{$value}' alt=''>
					</div>";
					$html .= "<input id='{$id}' class='{$class}' {$required} type='hidden' name='{$name}' value='{$value}' />";
					$html .= "<input id='{$id}_button' type='button' class='media-upload media-{$id}' value='Upload'>";
					$html .= "<input id='{$id}_clear' type='button' class='media-upload-clear media-{$id}-clear' value='Clear'>";
					break;
				case 'array' :
					echo "<select name='{$name}' id='{$id}'>";
					foreach( $valuesarray as $optionname => $label ){
						$checked = ( $value === $optionname ) ? 'checked="checked"' : '' ;
						echo "<option value='{$optionname}' {$checked} >{$label}</option>";
					}
					echo "</select>";
					break;
				default :
					break;
			}
			if ($description != ''){
				$html .= "<p class='description'>{$description}</p>";
			}
			$html .= $admin_after;
			echo $html;
		}
		public function create_sortable($name, $option){
			$fieldhtml = '';
			if ( empty( $option["sortable"] ) ){
				$this->_error("Error creating sortable field, setting missing sortable option for {$option['name']}");
				return 'Error creating sortable field, setting missing sortable option.';
			} else if ( empty( $option["sortable"]["post_type"] ) ){ 
				$this->_error( "Error creating sortable field, setting missing sortable post_type option for {$option['name']}" );
				return 'Error creating sortable field, setting missing sortable post_type option.';
			}
			$fieldhtml .= "<label>" . $option["label"] . "</label>";
			$value = $this->get_cache($name);
			$args = array(
				'post_type' => $option['sortable']["post_type"],
				'posts_per_page' => -1
			);

			// print_r($option);
			$fieldhtml .= "<div id='add-post-container-{$this->fieldnumber}' class='add-post'>";

			$fieldhtml .= "<input id='posts-" . $this->fieldnumber . "' type='text' autocomplete='false' class='add-field'><button id='add-post-" . $this->fieldnumber . "' href='' class='button button-primary add-btn' disabled='disabled'>Add</button>";

			$fieldhtml .= "</div>";

			$query = new WP_Query( $args );

			$fieldhtml .= "<ul id='sortable-{$this->fieldnumber}' class='sortable'>";
			$pc = 0; // post count for array indexis
			if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
			// $fieldhtml .= '<li class="post-' . get_the_id() . '">' . get_the_title() . '</li>';
			$sortposts[$pc]['title'] = get_the_title();
			$sortposts[$pc]['id'] = get_the_id();
			$sortposts[$pc]['id'] = get_the_id();
			$sortposts[$pc]['status'] = get_post_status();
			$indices[get_the_id()] = $pc;
			$pc++;
			endwhile; endif;
			$items = array();
			if (!empty($value)){
				$items = explode(",", $value);

			} else {
				// $fieldhtml .= '<div class="dragtome">Start typing in a post title above to add it to the list.</div>';
			}
			// print_r($value);
			// print_r($items);
			foreach ($items as $id){
				$currentpost = $sortposts[$indices[$id]];
				$status = ($currentpost['status'] !== "publish") ? " <span class=
			'status'>&#8212;{$currentpost['status']}</span>" : '';
				$fieldhtml .= '<li data-order="'.$indices[$id].'" data-id="'.$currentpost['id'].'" class="post-' . $currentpost['id'] . '">' . $currentpost['title'] . $status . '<div class="viewpost"><a href="'.admin_url("post.php").'?action=edit&post='.$currentpost['id'].'">edit</a></div><div class="exout">delete</div></li>';
			}

			$fieldhtml .= '</ul>';
			$fieldhtml .= '<div id="sortable-order-' . $this->fieldnumber . '">';
			$fieldhtml .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
			$fieldhtml .= '</div>';

			ob_start();
			?>
			<script>
				jQuery(document).ready(function($){
					var sortable = $('#sortable-<?php echo $this->fieldnumber ?>').sortable({
						stop: function(){
							var $self = $(this); // <ul class=sortable>
							updateOrder();
							
						},
						containment: "parent"
					});
					$(".exout").on("click", function(){
						removeLi(this);
					});
					var hiddenfield = $("#sortable-order-<?php echo $this->fieldnumber ?>");
					var removeLi = function(orgin){
						var $this = $(orgin);
						var parent = $this.parent();
						parent.fadeOut({
							complete: function(){
								$(this).remove();
								updateOrder();
							} 
						});
					}
					var updateOrder = function(){
						var lis = sortable.find("li");
						var l = lis.length - 1;
						var input,
							order = "";
						lis.each(function(i){
							var $this = $(this); // <li>
							$this.data("order", i);
							order += $this.data("id");
							if (i < l){ // if NOT last element
								order += ",";
							}
						});
						input = "<input type='hidden' name='<?php echo $name ?>' value='"+order+"' />";
						// console.log(input);
						hiddenfield.html($(input));
					}
					var ready = false; //if auto'plete has been used
					var posts = [
				<?php 
				$count = count($sortposts);
				foreach($sortposts as $post){
						$count --;
						echo "{
							label : '{$post['title']}',
							id : '{$post['id']}',
							status : '{$post['status']}' 
						}";
						if ($count > 0){
							echo ",";
						} 
				} ?>

					];

					var newfield = $( "#posts-<?php echo $this->fieldnumber ?>" );
					var addbtn = $("#add-post-<?php echo $this->fieldnumber ?>");
					// console.log(addbtn);
					addbtn.on("click", function(e){
						e.preventDefault();
						if (!ready) return; 
						var $this = $(this);
						if ($this.attr("disabled") == "disabled") return; 
						var lis = sortable.find("li");
						if (newfield.data("id")) {
							var stat = newfield.data("status");
							var status = ( stat != "publish" ) ? '<span class="status">&#8212;'+stat+'</span>' : "";
							var item = $('<li data-id="' + newfield.data("id") + '" data-order="' + lis.length + '" class="post-' + newfield.data("id") + '">' + newfield.data("label") + status + '<div class="viewpost"><a href="' + '<?php echo admin_url("post.php") ?>' + '?action=edit&post=' + newfield.data("id") +'">edit</a></div><div class="exout">delete</div></li>');
							item.find('.exout').click(function(){
								removeLi(this);
							});
							sortable.append(item);
							
							/*Reset stuff*/
								newfield.data("id", "");
								newfield.data("label", "");
								newfield.val("");
								newfield.removeClass("ready");
								addbtn.attr("disabled", "disabled");
								ready = false;
							/**/
							updateOrder(); // update hidden field
						} else {
							alert("Post data found, please try again.");
						}
					});
					// $( "#posts-<?php echo $this->fieldnumber ?>" ).suggest(posts); //???
					newfield.autocomplete({
						source : posts,
						select: function( event, ui ) {
							var $this = $(this);
							$this.data("id", ui.item.id);
							$this.data("label", ui.item.label);
							$this.data("status", ui.item.status);
							$this.addClass("ready");
							addbtn.removeAttr("disabled");
							ready = true;
						}
					});
					newfield.change(function(){ready = false});
					newfield.blur(function(){
						if (!ready) {// if NOT auto'pleted, clear the field
							newfield.val("");
							newfield.removeClass("ready");
						}
					});
				});
			</script>
			<?php
			$fieldhtml .= ob_get_clean();
			wp_reset_query();
			return $fieldhtml;

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
				$js_global = $this->get_localized_js_object_name();
				wp_localize_script(
					'lavaadminjs',
					$js_global,
					$this->set_admin_loc_js_values()
				);
				wp_enqueue_script('lavaadminjs');
			}
		}

	}/* EOF Class LavaOptions */
endif;

/*EOF*/