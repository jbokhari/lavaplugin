<?php
final class LavaOption_color extends LavaOption22 {
	public function get_option_field_html(){
		$value = $this->get_value();
		$value = esc_attr($value);
		$classes = $this->input_classes();
		$required = $this->required_html();
		$name = $this->name;
		$id = $this->id;
		$html = "";
		$html .= "<input class='lava-color-chooser'>;
		return $html;
	}
	public function validate($newValue = ""){
		return sanitize_file_name( $newValue );
	}
}