<?php if ( ! defined( "LAVAPLUGINPATH" ) ) {
  wp_die();
}

$dynamic = array( //array of available options

	/**************************************
	::::::::::::::::EXTERNAL:::::::::::::::
	**************************************/

	// "sometextfield" => array(
	//     'name' => 'sometextfield',
	// 	'label' => "Some TextField",
	// 	"type" => "str",
	// 	"class" => "test-class",
	// 	"tab" => 0,
	// 	"required" => true
	// ),
	// "featuredpost" => array(
	//     'name' => 'sortable',
	// 	'label' => "Sortable Field",
	// 	"default" => null,
	// 	"required" => false,
	// 	"type" => "sortable",
	// 	"sortable" => array(
	// 		"post_type" => "post"
	// 		),
	// 	"in_menu" => true,
	// 	"class" => "",
	// 	"tab" => 0,
	// 	"in_js" => false

	// ),
	// 'testcheckbox' => array(
	//     'name' => 'testcheckbox',
	// 	'label' => "Test CheckBox",
	// 	"default" => null,
	// 	"required" => false,
	// 	"type" => "bool",
	// 	"in_menu" => true,
	// 	"class" => "",
	// 	"tab" => 0,
	// 	"in_js" => false
	// ),
	// array(
	// 	'name' => 'test_array_1',
	// 	'label' => 'Test Array (radio)',
	// 	'type' => "array",
	// 	'ui' => 'radio',
	// 	'choices' => array(
	// 		array(
	// 		    "label" => "Choice 1",
	// 			"value" => "choice1"
	// 			),
	// 		array(
	// 		    "label" => "Choice 2",
	// 		    "value" => "choice2"
	//       		)
	// 	)
	// ),
	// array(
	// 	'name' => 'test_array_2',
	// 	'label' => 'Test Array (checkboxes)',
	// 	'type' => "array",
	// 	'ui' => 'checkboxes',
	// 	'choices' => array(
	// 		array(
	// 		    "label" => "Choice 1",
	// 			"value" => "choice1"
	// 			),
	// 		array(
	// 		    "label" => "Choice 2",
	// 		    "value" => "choice2"
	//       		)
	// 	)
	// ),
	// array(
	// 	'name' => 'test_array_3',
	// 	'label' => 'Test Array (select)',
	// 	'type' => "array",
	// 	'ui' => 'select',
	// 	'choices' => array(
	// 		array(
	// 		    "label" => "Choice 1",
	// 			"value" => "choice1"
	// 			),
	// 		array(
	// 		    "label" => "Choice 2",
	// 		    "value" => "choice2"
	//       		)
	// 	)
	// ),
	// array(
	// 	'name' => 'test_array_4',
	// 	'label' => 'Test Array (multiple)',
	// 	'type' => "array",
	// 	'ui' => 'multiple',
	// 	'choices' => array(
	// 		array(
	// 		    "label" => "Choice 1",
	// 			"value" => "choice1"
	// 			),
	// 		array(
	// 		    "label" => "Choice 2",
	// 		    "value" => "choice2"
	//       		)
	// 	)
	// ),
	// array(
	//       "name" => "number_field",
	//       "label" => "Number Field Test",
	//       "type" => "int",
	//       "rules" => array("max" => 200)
 //      ),
	// array(
	//       "name" => "email",
	//       "label" => "Email Field",
	//       "type" => "email"
 //      ),
	// array(
	//       "name" => "textarea",
	//       "label" => "Text Area",
	//       "type" => "textarea"
 //      ),
	array(
	      "name" => "image",
	      "label" => "Image",
	      "type" => "image"
	      ),
	// array(
	//       "name" => "color",
	//       "label" => "A Color",
	//       "type" => "color",
	//       "ui" => "wp"
 //      ),
	array(
		"name" => "Repeater",
		"label" => "Repeater",
		"type" => "repeater",
		"fields" => array(
			array(
				"name" => "subfield1",
				"label" => "Sub 1",
				"type" => "str"
			),
			// [0] => repeater [1] => sortable [2] => bool [3] => array [4] => image [5] => color [6] => repeater
			array(
			    "name" => "subfield2",
				"label" => "Sub 2",
				"type" => "str"	
			),
			array(
			    "name" => "email",
				"label" => "Email",
				"type" => "image"	
			),
			// array(
			//     "name" => "colorsub",
			// 	"label" => "Color Sub",
			// 	"type" => "color"	
			// ),
			// array(
			//     "name" => "Color Sub 2",
			// 	"label" => "Color Sub 2",
			// 	"type" => "color"	
			// ),
			// array(
			//     "name" => "subfield2",
			// 	"label" => "Sub 2",
			// 	"type" => "str"	
			// )
		)
      )
);

$static = array(
	'tabs' => array(
		0 => array(
			'label' => __('General Settings', 'text_domain'),
			'capability' => 'manage_options', //an idea, not in use
			'informational' => false
		)
	),
	'options_page' =>	array(
		'parent_slug' => 'options-general.php',
		'page_title'  => 'Featured Posts',
		'menu_title'  => 'Featured Posts',
		'capability'  => 'manage_options',
		'menu_slug'   => 'lava-plugin'
	)
);
/* EOF */