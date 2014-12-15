<?php
final class LavaOption_image extends LavaOption {
	public $requires_script = true;
	static $instance = 1;
	public function get_option_field_html(){
		$this->instance++;
		$value = $this->get_value();
		$value = esc_url_raw($value);
		$classes = $this->input_classes();
		$required = $this->required_html();
		$name = $this->name;
		$id = $this->id;
		$instance = $this->instance;
		$html = "";
		$fieldnumber = $this->fieldnumber;
		$html .= "<div id='image_{$id}_{$instance}_{$fieldnumber}_container' data-image-id='{$id}_{$instance}_{$fieldnumber}' class='image-container'>
			<img class='{$id}_{$instance}_{$fieldnumber}_preview image-preview {$id}_{$instance}-preview' src='{$value}' alt=''></div>";
		$html .= "<input class='{$id}_{$instance}_{$fieldnumber} image-source {$classes}' {$required} type='hidden' name='{$name}' value='{$value}' />";
		$html .= "<input type='button' class='{$id}_{$instance}_{$fieldnumber}_button media-upload media-{$id}_{$instance}' value='Upload'>";
		$html .= "<input type='button' class='{$id}_{$instance}_{$fieldnumber}_clear media-upload-clear media-{$id}_{$instance}-clear' value='Clear'>";
		return $html;
	}
	public function validate($newValue = ""){
		return sanitize_text_field( $newValue );
		return $newValue;
	}

	public function register_needed_scripts(){
		switch ( $this->ui ){
			case "default" :
				//scripts will be loaded from plugin/library/js/options
				$this->register_script( "lava.option.image.default.js" );
				break;
		}
	}
}