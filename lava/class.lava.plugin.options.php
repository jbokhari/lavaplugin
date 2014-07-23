<?php
/**
 * LavaOption22 basic elements of individual lava options. Contains two abstract methods
 * @abstract validate() converts new information to db safe value
 * @abstract get_option_field_html() generates html field for admin page
 * @package Lava
 * @version 2.2
 * @author Jameel Bokhari
 * @license GPL22
 */
abstract class LavaOption22 extends LavaLogging22 {
	public $name;
	public $label;
	public $id;
	public $value;
	public $type = "str";
	public $ui = "default";
	public $default = "";
	public $in_menu = "true";
	public $required = false;
	public $classes = array();
	public $in_js = false;
	public $tab = 0;
	protected $invalid = true;
	function __construct($prefix, array $options, $no = 0){
		// print_r($options);
		$this->_log("Instantiated");
		$this->prefix = $prefix;
		if ( isset( $options['name'] ) ){
			$this->name = $options['name'];
		} else {
			$this->_error("<code>Name</code> field not set for option {$this->name}. The label field and name are required.");
		}
		if ( isset( $options['label'] ) ){
			$this->label = $options['label'];
		} else {
			$this->_error("<code>Label</code> field not set for option {$this->name}. The label field and name are required.");
		} 
		$this->classes[] = "field-" . $no;
		$this->fieldnumber = $no;
		$this->id = $options['id'] = $this->prefix . $options['name'];
		$this->default_optionals($options);
		$this->init_tasks($options);
		$script = $this->get_single_instance_footer_scripts();
		if ($script)
			LavaCorePlugin22::set_si_footer_scripts($script);
	}
	protected function init_tasks($options){}
	public function get_option_label_html(){
		$html = "";
		$required = $this->required ? "*" : "";
		$html .= "<label for='{$this->id}'>{$this->label}{$required}</label>";
		return $html;
	}
	public function get_form_js(){
		return "";
	}
	final public function get_option_header_html(){
		return "<div class='option-block field-{$this->fieldnumber}'>";
	}
	final public function get_option_footer_html(){
		$return = $this->get_form_js();
		$return = "<div style='clear:both;'></div>";
		$return = "</div>";
		return $return;
	}
	/**
	 * Used by LavaPlugin class to queue JavaScript to be appended to the options page when this option is loaded. These scripts are defined in this function when a script is needed to be run only one time for no matter how many options of this type are created.
	 * @return (string)
	 */
	public function get_single_instance_footer_scripts(){}
	final private function delete_value(){
		return delete_option( $this->id );
	}
	public function default_optionals($options){
		$this->_log("Run default_optionals()");
		if ( isset( $options['type'] ) )
			$this->type = $options['type'];
		if ( isset( $options['default'] ) )
			$this->default = $options['default'];
		if ( isset( $options['in_menu'] ) )
			$this->in_menu = $options['in_menu'];
		if ( isset( $options['class'] ) ){
			if( is_array( $options['class'] ) )
				array_merge($this->classes, $options['class']);
			else
				array_push($this->classes, $options['class'] );
		}
		if ( isset( $options['in_js'] ) )
			$this->in_js = $options['in_js'];
		if ( isset( $options['tab'] ) )
			$this->tab = $options['tab'];
		if ( isset( $options['required'] ) )
			$this->required = $options['required'];
	}
	final public function get_value($default = null){
		if ($default === null)
			$default = $this->default;
		if( ! $this->value){
			$value = get_option($this->id, $default);
			$this->value = $this->output_filter($value);
		}
		return $this->value;
	}
	/**
	 * Override output_filter() rather than rewrite get_value()
	 * @param type $input 
	 * @return type
	 */
	public function output_filter($input){
		return $input;
	}
	public function add_class($class){
		$this->classes[] = $class;
	}
	public function is_required(){
		if ($this->required){
			$this->_error("set_value() could not be performed on {$this->name} because it is required and the value was empty after validation.");
			$this->invalidate();
			return true;
		} else {
			return false;
		}
	}
	protected function invalidate($msg = ""){
		$this->invalid = true;
		if ($msg != ""){
			$this->error_tooltip = $msg;
		}
		$this->add_class('invalid');
	}
	public function set_value($newValue = ""){
		$this->_log("set_value() was run.");
		$newValue = $this->validate($newValue);
		if ( $newValue == "" && $this->is_required() )
			return false;
		return update_option($this->id, $newValue);
	}
	public function input_classes(){
		$temp = array();
		foreach ($this->classes as $class){
			$class = sanitize_html_class( $class );
			$temp[] = $class;
		}
		$classes = implode(" ", $temp);
		return $classes;
	}
	protected function required_html(){
		return $this->required ? 
			"required='required'" :
			"";
	}
	abstract public function validate($newValue = "");
	abstract public function get_option_field_html();
}

/* EOF */