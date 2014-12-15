<?php
class LavaOption_repeater extends LavaOption {
	public $rows = 1;
	public $script = array();
	function init_tasks($options){
		if ( isset( $options['fields']) && ! empty( $options['fields'] ) &&  is_array( $options['fields'] ) ){
			$i = 0;
			foreach ( $options['fields'] as $f ){
				$unsupported = array("repeater", "sortable", "bool", "array", "repeater", "color");
				if ( in_array($f['type'], $unsupported)){
					$this->_error("LavaPlugin error: the repeater field does not currently support elements of type {$f['type']}. Please remove this option in your settings file or change it to a supported element. Unsupported elements include: " . join(", ", $unsupported) ) . ".";
					continue;
				}
				$f['id'] = "{$f['name']}";
				if ( isset( $this->fields[$f['id']] ) )
					$this->_error("The repeater field <code>{$this->name}</code> already has a subfield with the id <code>{$f['id']}</code>, it has been overridden with latest given arguments.");
				$f['name'] = $this->name . "[{$f['name']}][]";
				$this->fields[$f['id']] = LavaFactory::create("", $f );
				$i++;
			}   
			$count = count($this->fields);
			if ( $count < 10 ){
				$this->add_outer_class("col-1of{$count}");
				foreach($this->fields as $f){
					$f->add_label_class("col-1of{$count}");
				}
				$this->column_width = $count;
			}
			else{	
				$this->_error("Too many sub fields assigned to option {$this->name}. Ten is the currently supported maximum.");
			}
		}
	}
	/**
	 * Unserialize and convert data to $this->{OPTION}->value
	 * @return void
	 */
	public function output_filter($newValue){
		$this->logger->_log("understand_values() started.");
		$values = unserialize( $newValue );
		if ( isset( $values['__meta_rows'] ) && !empty( $values['__meta_rows'] ) ){
			$this->rows = $values['__meta_rows'];
		} else {
			$this->rows = 1;
		}
		for ($i = 0; $i < $this->rows; $i++) {
			foreach ( $this->fields as &$f ){
				if ( !isset($f->values) || ! is_array($f->values) ){
					$f->values = array();
				}
				if ( !isset( $values[$i][$f->id] ) || empty( $values[$i][$f->id] ) )
					$thisvalue = "";
				else 
					$thisvalue = $values[$i][$f->id];
				$f->values[$i] = $thisvalue;
			}
		}
		//return blank, the options are stored differently
		return "";
	}
	// public function output_filter($value){
	// 	return $this->understand_values();
	// }
	public function set_column_widths(){
		foreach ($this->fields as $f) {
			$column_width_class = "col-1of{$this->column_width}"; 
			// $f->add_class($column_width_class);
			$f->add_class("repeater-subfield");
		}
	}
	public function get_option_field_html(){
		$this->logger->_log("get_option_field_html() started.");
		// $this->set_value(array(
		// 		"_rows" => 2,
		// 		""

		// 	)
		// print_r($this->fields);
		$values = $this->get_value();

		$html = "";
		$html .= "<div id='{$this->id}-fields' class='cf repeater-field-fields column-count-{$this->column_width}'>";
		$html .= "<div class='repeater-head cf'>";
		foreach ($this->fields as $f){
			$html .= $f->get_option_label_html();
		}
		$html .= "</div>";
		$html .= "<ul>";
		// print_r($this);
		// print_r($this->fields);
		$this->set_column_widths();
		for ($i = 0; $i < $this->rows; $i++) { 
			$html .= "<li class='repeater-row cf'>";
			$html .= "<div class='handle'></div>";
				$html .= "<div class='row-fields'>";
					foreach ($this->fields as $f){
						$html .= "<div class='" . $this->get_outer_class() . " repeater-col cf'>";
						$f->value = $f->values[$i];
						$html .= $f->get_option_field_html();
						$html .= "</div>";
					}

				$html .= "	</div>";
			$html .= "</li>";//end row
		}
		// print_r($this->fields);
		$html .= "</ul>";
		$html .= "</div>";//end container
		$html .= "<input id='{$this->id}__meta_rows' type='hidden' name='{$this->name}[__meta_rows]' value='{$this->rows}'>";//end container
		$html .= "<div class='cf button-container'>";
		$html .= "<button data-id='$this->id' class='repeater-add'>Add Fields</button>";
		$html .= "</div>";
		return $html;
	}
	public function validate($newValue = ""){
		$rows = $newValue["__meta_rows"];
		$fixedNewValue = array();
		for ($i = 0; $i < $rows; $i++) {
			$fixedNewValue[$i] = array();
			foreach ($this->fields as $subfield ) {
				$value = $subfield->validate($newValue[$subfield->id][$i]);
				$fixedNewValue[$i][$subfield->id] = $value;
				// var_dump($value);
			}
		}
		$fixedNewValue["__meta_rows"] = $rows;
		$newValue = serialize($fixedNewValue);
		return $newValue;
	}
	public function register_needed_scripts(){
		switch ( $this->ui ){
			case "default" :
				//scripts will be loaded from plugin/library/js/options
				$this->register_script( "lava.option.repeater.default.js" );
				break;
		}
	}
}