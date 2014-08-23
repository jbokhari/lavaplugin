jQuery(document).ready(function($){
	//clone button
	$('.repeater-add').each(function(){
		var id = $(this).data("id");
		var repeatercount = $('#' + id + '__meta_rows');
		$(this).on('click', function(e){
			var id = $(this).data("id");
			e.preventDefault();
			var container = $("#" + id + "-fields ul");
			var rows = container.find(".repeater-row");
			var clone = rows.last().clone();
			clone.find("[type='hidden'], [type='text'], [type='email'], [type='number'], [type='password'], [type='url'], [type='date'], [type='text'], textarea").val("");
			clone.find("[type='checkbox'],[type='radio']").removeAttr('checked');
			clone.find(".lava-color-chooser").val('');
			clone.find("select").removeAttr("selected");
			clone.appendTo(container);
			var rows = parseInt( repeatercount.val() ) + 1;
			repeatercount.val(rows);
		}).after(repeatercount);
	});

	var data = {};
	// $("#<?php echo $this->id ?>-fields .repeater-row").each(function(i){
	// 	var rowID = "row_"+i;
	// 	data[rowID] = {};
	// 	var $this = $(this);
	// 	var $inputs = $this.find("[type='hidden'], [type='text'], [type='email'], [type='number'], [type='password'], [type='url'], [type='date'], [type='text'], textarea");
	// 	var inputData = $inputs.each(function(){
	// 		var $self = $(this);
	// 		var name = $self.name;
	// 		data[rowID][name] = $self.val(); 
	// 	});
	// 	var $boxes = $this.find("[type='checkbox'],[type='radio']");
	// 	$boxes.each(function(){
	// 		var $self = $(this);
	// 		var name = $self.name;
	// 	});
	// 	var $selects = $this.find("select");
	// });

});
