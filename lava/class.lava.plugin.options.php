<?php
abstract class LavaOption22 extends LavaLogging22 {
	public $name;
	public $label;
	public $id;
	public $value;
	public $type = "str";
	public $ui = "default";
	public $default = "";
	public $in_menu = "true";
	public $classes = array();
	public $in_js = false;
	public $tab = 0;
	function __construct($prefix, array $options){
		// print_r($options);
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
		$this->id = $options['id'] = $this->prefix . $options['name'];
		$this->default_optionals($options);
	}
	final private function delete_value(){
		return delete_option( $this->id );
	}
	public function default_optionals($options){
		if ( isset( $options['type'] ) )
			$this->type = $options['type'];
		if ( isset( $options['default'] ) )
			$this->default = $options['default'];
		if ( isset( $options['in_menu'] ) )
			$this->in_menu = $options['in_menu'];
		if ( isset( $options['class'] ) ){
			if( is_array( $options['class'] ) )
				$this->class = array_merge($this->classes, $options['class']);
			else
				$this->class = array_push($this->classes, $options['class'] );
		}
		if ( isset( $options['in_js'] ) )
			$this->in_js = $options['in_js'];
		if ( isset( $options['tab'] ) )
			$this->tab = $options['tab'];
	}
	public function get_value($default = null){
		if ($default === null)
			$default = $this->default;
		if( ! $this->value)
			$this->value = get_option($this->id, $default);
		return $this->value;
	}
	protected function is_required(){
		if ($this->required){
			$this->_error("set_value() could not be performed on {$this->name} because it is required and the value was empty after validation.");
			return true;
		} else {
			return false;
		}
	}
	protected function set_value($newValue = ""){
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
	abstract public function option_field_html();
}
final class LavaOption_str extends LavaOption22 {
	public function option_field_html(){
		$value = $this->get_value();
		$value = esc_attr($value);
		$classes = $this->input_classes();
		$required = $this->require_html();
		$name = $this->name;
		$id = $this->id;
		return "<input id='{$id}' class='{$classes}' {$required} type='text' name='{$name}' value='{$value}' />";
	}
	public function validate($newValue = ""){
		/* later we can add better validation here like string length, zip code validation and stuff like that */
		return sanitize_text_field( $newValue );
	}
}
final class LavaOption_url extends LavaOption22 {
	public function option_field_html(){
		$value = $this->get_value();
		$value = esc_attr($value);
		$classes = $this->input_classes();
		$required = $this->require_html();
		$name = $this->name;
		$id = $this->id;
		return "<input id='{$id}' class='{$classes}' {$required} type='url' name='{$name}' value='{$value}' />";
	}
	public function validate($newValue = ""){
		return esc_url_raw( $newValue );
	}
}
final class LavaOption_array extends LavaOption22 {
	function __construct($prefix, array $options){
		parent::__construct($prefix, $options);
		$this->array_init($options);
	}
	public function array_init($options){
		//backwards compaitibility, or to level confusiion over settings
		if ( ! isset( $options['choices'] ) )
			if ( isset( $options['values'] ) )
				$this->choices = $options['values'];
			else 
				$this->_error("Lava option with array type needs choices specified. None were set.");
		else
			$this->choices = $options['choices'];
		$this->ui = "select"; // default ui is select
		if ( $options['ui'] )
			$this->ui = $options['ui'];
	}
	public function multiple_html(){
		if ( $this->ui == "multiple" ){
			// xhtml valid
			return "multiple='multiple'";
		} else {
			return "";
		}
	}
	public function selected_html($choice){
		if ($this->get_value() == $choice){
			return "selected='selected'";
		} else {	
			return "";
		}
	}
	public function checked_html($choice){
		if ($this->get_value() == $choice){
			return "checked='checked'";
		} else {	
			return "";
		}
	}
	public function get_choice_slug($label){
		return esc_attr( $label );
	}
	public function get_value($default = null){
		if ($default === null)
			$default = $this->default;
		if( ! $this->value)
			$this->value = unserialize( get_option($this->id, $default) );

		return $this->value;
	}
	public function option_field_html(){
		$value = $this->get_value();
		$value = esc_attr($value);
		$classes = $this->input_classes();
		$required = $this->require_html();
		$name = $this->name;
		$id = $this->id;
		$multiple = $this->multiple_html;
		$html = "";
		switch($this->ui) {
			case "multiple" :
			case "select" :
				$html .= "<select id='{$id}' class='{$classes}' {$multiple} {$required} type='url' name='{$name}[]'>";
				foreach ($this->$choices as $c){
					$val = $c["value"];
					$label = $c["label"];
					$selected = $this->selected_html($val);
					$html .= "<option value='{$val}'>{$label}</option>";
				}
				$html .= "</select>";
				break;
			case "checkboxes" :
				$html .= "<div class='{$classes} checkboxes'>";
				foreach ($this->$choices as $c){
					$val = $c["value"];
					$label = $c["label"];
					$checked = $this->checked_html($val);
					$choiceID = $this->id . "-" . $this->get_choice_slug($label);
					$html .= "<label for='{$choiceID}'>$label</label>";
					$html .= "<input id='{$choiceID}' type='checkbox' name='{$name}[]' value='{$val}' />";
				}
				$html .= "</div>";
				break;
			case "radio" :
				$html .= "<div class='{$classes} radios'>";
				foreach ($this->$choices as $c){
					$val = $c["value"];
					$label = $c["label"];
					$checked = $this->checked_html($val);
					$choiceID = $this->id . "-" . $this->get_choice_slug($label);
					$html .= "<label for='{$choiceID}'>$label</label>";
					$html .= "<input id='{$choiceID}' type='radio' name='{$name}[]' value='{$val}' />";
				}
				$html .= "</div>";
				break;
			default :
				$this->_error("No ui specified, or ui did not match one of the built in options. Please specify a valid UI type when creating an array type option.");
				return "";
				break;
		}
	}
	public function is_valid_choice_value($val){
		foreach ($this->choices as $choice){
			if ( $choice["value"] == $val )
				return true;
		}
		return false;
	}
	public function validate($newValue = ""){
		$valid = true;
		$confirmedValues = array();
		if ( is_array($newValue) ){
			if ( $this->ui == "multiple" || $this->ui == "checkboxes" ){
				// mulitiple uptions allowed
				foreach ($newValue as $val){
					if ( $this->is_valid_choice_value($val) ){
						$confirmedValues[] = $val;
					} else {
						$valid = false;
					}
				}
			//otherwise, multiple values are not allowed
			} else {
				// just use the first option.
				$confirmedValues[] = $newValue[0]; 
				$this->_log("The array type LavaOption $this->name received multiple values to save the ui should not allow it. Refferring to first option instead, but this could represent a problem or the form was submitted falsely.");
			}
		} else {
			$valid = false;
			$this->_log("The array type LavaOption $this->name received something other than an array when validating. This is an error with the plugin and should not happen.");
			$newValue = strval($newValue);
			if ( $this->is_valid_choice_value($newValue) )
				$confirmedValues = $newValue;
		}
		// if not valid, dont' worry now just return validated value
		$values = serialize($confirmedValues);
		return $values;
	}
}
final class LavaOption_textarea extends LavaOption22 {
	public function option_field_html(){
		"";
	}
	public function validate($newValue = ""){
		return true;
	}
}
final class LavaOption_int extends LavaOption22 {
	public function option_field_html(){
		"";
	}
	public function validate($newValue = ""){
		return true;
	}
}
final class LavaOption_sortable extends LavaOption22 {
	public function option_field_html(){
		"";
	}
	public function validate($newValue = ""){
		return true;
	}
}
final class LavaOption_image extends LavaOption22 {
	public function option_field_html(){
		"";
	}
	public function validate($newValue = ""){
		return true;
	}
}
final class LavaOption_email extends LavaOption22 {
	public function option_field_html(){
		"";
	}
	public function validate($newValue = ""){
		return true;
	}
}
final class LavaOption_bool extends LavaOption22 {
	public function option_field_html(){
		"";
	}
	public function validate($newValue = ""){
		return true;
	}
}
final class LavaFactory {
	static function create($prefix, array $options ){
		$type = $options['type'];
		if (!$type)
			return false;
		$object = "LavaOption_{$type}";
		return new $object($prefix, $options);
	}
}

/* EOF */