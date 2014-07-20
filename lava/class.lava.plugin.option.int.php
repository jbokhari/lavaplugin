<?php
final class LavaOption_int extends LavaOption22 {

	public $rules = array();

	public function __construct($prefix, array $options){
		parent::__construct($prefix, $options);
		$this->int_init($options);
	}
	public function int_init($options){
		if ( isset($options['rules']) ){
			if ( isset( $options['rules']['min'] ) ){
				$this->rules['min'] = intval( $options['rules']['min'] );
			}
			if ( isset( $options['rules']['max'] ) ){
				$this->rules['max'] = intval( $options['rules']['max'] );
			}
			if ( isset( $options['rules']['step'] ) ){
				$this->rules['step'] = intval( $options['rules']['step'] );
			}
		}
	}
	public function generate_maximum_value_html(){
		if ( isset( $this->rules['min'] ) ){
			return "min='{$this->rules['min']}'";
		} else {
			return "";
		}
	}
	public function generate_minimum_value_html(){
		if ( isset( $this->rules['max'] ) ){
			return "max='{$this->rules['max']}'";
		} else {
			return "";
		}
	}
	public function generate_step_value_html(){
		if ( isset( $this->rules['step'] ) ){
			return "step='{$this->rules['step']}'";
		} else {
			return "";
		}
	}
	public function get_option_field_html(){
		$value = $this->get_value();
		$value = esc_attr($value);
		$classes = $this->input_classes();
		$required = $this->required_html();
		$min = $this->generate_minimum_value_html();
		$max = $this->generate_maximum_value_html();
		$step = $this->generate_step_value_html();
		$name = $this->name;
		$id = $this->id;
		return "<input id='{$id}' class='{$classes}' {$required} type='number' $max $min $step name='{$id}' value='{$value}' />";
	}
	public function validate($newValue = ""){
		return sanitize_email( $newValue );
	}
}