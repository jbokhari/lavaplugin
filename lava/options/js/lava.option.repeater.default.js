jQuery(document).ready(function($){
	//clone button
	$('.repeater-add').each(function(){
		var $this = $(this);
		var id = $this.data("id");
		var repeatercount = $('#' + id + '__meta_rows');
		var container = $("#" + id + "-fields ul");
		container.find("li").each(function(){
			var exout = $("<span>x</span>").on("click", function(){
				var r = confirm("Are you sure you want to delete this field?");
				if (r)
					$(this).parent().remove();
				else
					return;
				var rows = parseInt( repeatercount.val() ) - 1;
				repeatercount.val(rows);
			});
			$(this).append(exout);
		})
		console.log("test");
		$this.on('click', function(e){
			console.log("test");
			e.preventDefault();
			var id = $(this).data("id");
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
		// var sortables = $('.lava-sortable');
		container.sortable({
			stop: function(){
				var $self = $(this); // <ul class=sortable>
				updateOrder($self);
			},
     		handle: ".handle",
			containment: "parent"
		});
	});
	var updateOrder = function(){
		return;
	}
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
