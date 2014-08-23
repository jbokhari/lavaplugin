<?php
final class LavaOption_bool extends LavaOption22 {
	public function get_option_field_html(){
		$value = $this->get_value();
		$classes = $this->input_classes();
		$required = $this->required_html();
		$checked = $this->checked_html();
		$name = $this->name;
		$id = $this->id;
		$this->checked_html();
		return "<input id='{$id}' class='{$classes}' {$checked} {$required} type='checkbox' name='{$name}' value='1' />";
	}
	public function is_required(){
		return false;
	}
	public function checked_html(){
		if ($this->get_value() == "true"){
			return "checked='checked'";
		} else {	
			return "";
		}
	}
	public function validate($value = null){
		if ($value && $value != "false")
			return "true";
		else
			return "false";
	}
}