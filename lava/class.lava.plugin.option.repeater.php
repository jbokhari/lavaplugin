<?php
class LavaOption_repeater extends LavaOption22 {
	public $rows = 1;
	function init_tasks($options){
		if ( isset($options['fields']) && ! empty( $options['fields'] ) &&  is_array( $options['fields'] ) ){
			foreach ($options['fields'] as $f){
				// print_r($f);
				//unsupported options currently
				$unsupported = array("repeater", "sortable", "bool", "array", "repeater", "color");
				if (in_array($f['type'], $unsupported)){
					$this->_error("LavaPlugin error: the repeater field does not currently support elements of type {$f['type']}. Please remove this option in your settings file or change it to a supported element. Unsupported elements include: " . join(", ", $unsupported) ) . ".";
					continue;
				}
				$f['id'] = "{$f['name']}";
				if ( isset( $this->fields[$f['id']] ) )
					$this->_error("The repeater field <code>{$this->name}</code> already has a subfield with the id <code>{$f['id']}</code>, it has been overridden with latest given arguments.");
				$f['name'] = $this->name . "[{$f['name']}][]";
				$this->fields[$f['id']] = LavaFactory::create("", $f );
			}
			$count = count($this->fields);
			if ($count < 7){
				$this->add_outer_class("col-1of{$count}");
				$this->column_width = $count;
			}
			else{	
				$this->_error("Too many sub fields assigned to option {$this->name}. Seven is the currently supported maximum.");
			}
			//This is running too early, so when data saves, get_value() already has done it's thing and saves the wrong ammount of rows.
			// $this->understand_values();
		}
		// echo "<pre>";
		// // print_r($this);
		// foreach($this->fields as $field){
		// 	$f = $this->id . "_" . $field->name;
		// 	// print_r( $f );
		// 	// print_r( $_POST[$f] );
		// }
		// echo "</pre>";
	}
	/**
	 * Unserialize and convert data to $this->{OPTION}->value
	 * @return void
	 */
	public function output_filter($newValue){
		$this->_log("understand_values() started.");
		$values = unserialize( $newValue );
		// var_dump($values);
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
			$f->add_class($column_width_class);
			$f->add_class("repeater-subfield");
		}
	}
	public function get_option_field_html(){
		$this->_log("get_option_field_html() started.");
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
				foreach ($this->fields as $f){
					$f->value = $f->values[$i];
					$html .= $f->get_option_field_html();
				}

			$html .= "</li>";//end row
		}
		// print_r($this->fields);
		$html .= "</ul>";
		$html .= "</div>";//end container
		$html .= "<input id='{$this->id}__meta_rows' type='hidden' name='{$this->name}[__meta_rows]' value='{$this->rows}'>";//end container
		// var_dump($this->rows);
		$html .= "<div class='cf button-container'>";
		$html .= "<button data-id='$this->id' class='repeater-add'>Add Fields</button>";
		$html .= "</div>";
		return $html;
	}
	public function get_single_instance_footer_scripts(){
		if ( ! empty( self::$single_instance_scripts[$this->ui] ) )
			return;
		self::$single_instance_scripts[$this->ui] = true;
		switch ( $this->ui ){
			case "default" :
				//scripts will be loaded from plugin/library/js/options
				return "lava.option.repeater.default.js";
				break;
		}
		return false; //default return false
	}
	// public function set_value($newValue = ""){
		// $this->validate($newValue);
	// }
	// 	foreach($this->fields as $field){
	// 		// $this->
	// 		var_dump($field);
	// 	}
	// }
	public function validate($newValue = ""){
		// var_dump($newValue);
		$rows = $newValue["__meta_rows"];
		$fixedNewValue = array();
		for ($i = 0; $i < $rows; $i++) {
			$fixedNewValue[$i] = array();
			foreach ($this->fields as $subfield ) {
				// var_dump($newValue[$subfield->id][$i]);
				$value = $subfield->validate($newValue[$subfield->id][$i]);
				$fixedNewValue[$i][$subfield->id] = $value;
				// var_dump($value);
			}
		}
		$fixedNewValue["__meta_rows"] = $rows;
		// var_dump($fixedNewValue);
		$newValue = serialize($fixedNewValue);
		// var_dump($newValue);
		return $newValue;
	}
}