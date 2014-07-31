<?php
final class LavaOption_repeater extends LavaOption22 {
	public $rows = 1;
	function init_tasks($options){
		if ( isset($options['fields']) && ! empty( $options['fields'] ) &&  is_array( $options['fields'] ) ){
			foreach ($options['fields'] as $f){
				//unsupported options currently
				$unsupported = array("repeater", "sortable", "bool", "array", "image", "color", "repeater");
				if (in_array($f['type'], $unsupported)){
					$this->_error("LavaPlugin error: the repeater field does not currently support elements of type {$f['type']}. Please remove this option in your settings file or change it to a supported element. Unsupported elements include: " . print_r($unsupported,true));
					return;
				}
				$f['id'] = $this->id . "_" . $f['name'] . "[]";
				$this->fields[] = LavaFactory::create("", $f );
			}
		}
		echo "<pre>";
		// print_r($this);
		foreach($this->fields as $field){
			$f = $this->id . "_" . $field->name;
			// print_r( $f );
			// print_r( $_POST[$f] );
		}
		echo "</pre>";
	}
	public function get_option_field_html(){
		$html = "";
		$html .= "<div id='{$this->id}-fields' class='cf repeater-field-fields'>";
		$html .= "<div class='repeater-row cf'>";
		for ($i = 0; $i < $this->rows; $i++) { 
			foreach ($this->fields as $f){
				$html .= $f->get_option_field_html();
			}
		}
		$html .= "</div>";//end row
		$html .= "</div>";//end container
		$html .= "<div class='cf button-container'>";
		$html .= "<button data-id='$this->id' class='repeater-add'>Add Fields</button>";
		?>
		<script>
		jQuery(document).ready(function($){
			//clone button
			$('.repeater-add').on('click', function(e){
				e.preventDefault();
				var id = $(this).data("id");
				console.log(id);
				var container = $("#" + id + "-fields");
				console.log(container);
				var rows = container.find(".repeater-row");
				console.log(rows);
				var clone = rows.last().clone();
				console.log(clone);
				clone.find("[type='hidden'], [type='text'], [type='email'], [type='number'], [type='password'], [type='url'], [type='date'], [type='text'], textarea").val("");
				clone.find("[type='checkbox'],[type='radio']").removeAttr('checked');
				clone.find("select").removeAttr("selected");
				clone.appendTo(container);
			});

			var data = {};
			$("#<?php echo $this->id ?>-fields .repeater-row").each(function(i){
				var rowID = "row_"+i;
				data[rowID] = {};
				var $this = $(this);
				var $inputs = $this.find("[type='hidden'], [type='text'], [type='email'], [type='number'], [type='password'], [type='url'], [type='date'], [type='text'], textarea");
				var inputData = $inputs.each(function(){
					var $self = $(this);
					var name = $self.name;
					data[rowID][name] = $self.val(); 
				});
				var $boxes = $this.find("[type='checkbox'],[type='radio']");
				var $boxes.each(function(){
					var $self = $(this);
					var name = $self.name;
				});
				var $selects = $this.find("select");
				console.log($this);
			});

		});

		</script>
		<?php
		$html .= "</div>";
		return $html;
	}
	public function validate($newValue = ""){
		foreach($this->fields as $field){
			
		}
	}
}