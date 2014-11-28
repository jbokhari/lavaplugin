<?php
final class LavaOption_image extends LavaOption {
	public function get_option_field_html(){
		$value = $this->get_value();
		$value = esc_url_raw($value);
		$classes = $this->styler->input_classes();
		$required = $this->required_html();
		$name = $this->name;
		$id = $this->id;
		$html = "";
		$html .= "<div class='image-container'>
			<img id='{$id}_preview' class='image-preview {$id}-preview' src='{$value}' alt=''></div>";
		$html .= "<input id='{$id}' class='{$classes}' {$required} type='hidden' name='{$name}' value='{$value}' />";
		$html .= "<input id='{$id}_button' data-id='{$id}' type='button' class='media-upload media-{$id}' value='Upload'>";
		$html .= "<input id='{$id}_clear' type='button' class='media-upload-clear media-{$id}-clear' value='Clear'>";
		return $html;
	}
	public function validate($newValue = ""){
		return sanitize_text_field( $newValue );
		return $newValue;
	}
	public function get_single_instance_footer_scripts(){
		if ( ! empty( self::$single_instance_scripts[$this->ui] ) )
			return;
		self::$single_instance_scripts[$this->ui] = true;
		switch ( $this->ui ){
			case "default" :
				//scripts will be loaded from plugin/library/js/options
				return "lava.option.image.default.js";
				break;
		}
		return false; //default return false
	}
}