<?php
final class LavaOption_repeater extends LavaOption22 {
	function init_tasks($options){
		if ( isset($options['fields']) && ! empty( $options['fields'] ) &&  is_array( $options['fields'] ) ){
			foreach ($options['fields'] as $f){
				$this->fields[] = LavaFactory::create($this->prefix, $f );
			}
		}
		print_r($this);
	}
	public function get_option_field_html(){
		$html = "";
		foreach ($this->fields as $f){
			$html .= $f->get_option_field_html();
		}
		return $html;
	}
	public function validate($newValue = ""){

	}
}