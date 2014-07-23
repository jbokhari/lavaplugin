<?php
final class LavaOption_color extends LavaOption22 {
	public $ui;
	public $scripts = array(
        array( "jscolor", "libs/jscolor/jscolor.js", array() ),
        array( "rgbapicker", "libs/colorpickerrgba/rgbacolorpicker.min.js", array("jquery") )
	);
	public static $single_instance_scripts = array();
	public $styles = array(
		array("rgbapicker", "libs/colorpickerrgba/rgbacolorpicker.css", array())
    );
	public function init_tasks($options){
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts_and_styles' ) );
		$this->ui = isset($options['ui']) ? $options['ui'] : "hex";
		if ($this->ui == "rgba")
			$this->add_class("rgbacolorpicker");
		else if ($this->ui == "hex")
			$this->add_class("lava-color-chooser");
	}
	public function get_single_instance_footer_scripts(){
		if ( $this->ui == "rgba" && empty(self::$single_instance_scripts[$this->ui]) ){
			self::$single_instance_scripts[$this->ui] = true;
			return "jQuery('input.rgbacolorpicker').rgbacolorpicker();";
		}
		return false; //default return false
	}
	public function get_option_field_html(){
		$value = $this->get_value();
		$value = esc_attr($value);
		$classes = $this->input_classes();
		$required = $this->required_html();
		$name = $this->name;
		$id = $this->id;
		$html = "";
		$html .= "<input name='{$id}' value='{$value}' class='{$classes}'>";
		return $html;
	}
	public function enqueue_scripts_and_styles(){
		foreach ($this->styles as $style){
			list($handle, $source, $dependencies) = $style;
			$path = LavaCorePlugin22::get_css_dir();
			$fullpath = $path . $source;
			wp_register_style( $handle, $fullpath, $dependencies );
		}
		foreach ($this->scripts as $script){
			list($handle, $source, $dependencies) = $script;
			$path = LavaCorePlugin22::get_js_dir();
			$fullpath = $path . $source;
			wp_register_script( $handle, $fullpath, $dependencies );
		}
		if ($this->ui == "hex" ){
			wp_enqueue_script( "jscolor" );
		}
		if ($this->ui == "rgba" ){
			wp_enqueue_script( "rgbapicker" );
			wp_enqueue_style( "rgbapicker" );
		}
	}
	public function validate($newValue = ""){
		return sanitize_file_name( $newValue );
	}
}