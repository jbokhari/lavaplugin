<?php if ( ! defined( "ECT_RELATED_CONTENT_PATH" ) ) {
  wp_die();
}

$dynamic = array( //array of available options

	/**************************************
	::::::::::::::::EXTERNAL:::::::::::::::
	**************************************/

	"sometextfield" => array(
	    'name' => 'sometextfield',
		'label' => "Some TextField",
		"type" => "str",
		"class" => "test-class",
		"tab" => 0
	),
	// "featuredpost" => array(
	//     'name' => 'featuredpost',
	// 	'label' => "Featured Posts",
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
	'testcheckbox' => array(
	    'name' => 'testcheckbox',
		'label' => "Test CheckBox",
		"default" => null,
		"required" => false,
		"type" => "bool",
		"in_menu" => true,
		"class" => "",
		"tab" => 0,
		"in_js" => false
	),
	array(
		'name' => 'test_array_1',
		'label' => 'Test Array (radio)',
		'type' => "array",
		'ui' => 'radio',
		'choices' => array(
			array(
			    "label" => "Choice 1",
				"value" => "choice1"
				),
			array(
			    "label" => "Choice 2",
			    "value" => "choice2"
	      		)
		)
	),
	array(
		'name' => 'test_array_2',
		'label' => 'Test Array (checkboxes)',
		'type' => "array",
		'ui' => 'checkboxes',
		'choices' => array(
			array(
			    "label" => "Choice 1",
				"value" => "choice1"
				),
			array(
			    "label" => "Choice 2",
			    "value" => "choice2"
	      		)
		)
	),
	array(
		'name' => 'test_array_3',
		'label' => 'Test Array (select)',
		'type' => "array",
		'ui' => 'select',
		'choices' => array(
			array(
			    "label" => "Choice 1",
				"value" => "choice1"
				),
			array(
			    "label" => "Choice 2",
			    "value" => "choice2"
	      		)
		)
	),
	array(
		'name' => 'test_array_4',
		'label' => 'Test Array (multiple)',
		'type' => "array",
		'ui' => 'multiple',
		'choices' => array(
			array(
			    "label" => "Choice 1",
				"value" => "choice1"
				),
			array(
			    "label" => "Choice 2",
			    "value" => "choice2"
	      		)
		)
	),
	array(
	      "name" => "number_field",
	      "label" => "Number Field Test",
	      "type" => "int",
	      "rules" => array()
      )
	// 'credit3' => array(
	// 	'label' => 'Empty Text Field',
	// 	'default' => false,
	// 	'required' => false,
	// 	'type' => 'str',
	// 	'in_menu' => true,
	// 	'class' => '',
	// 	'tab' => 0,
	// 	'in_js' => false
	// )
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
		'menu_slug'   => 'related-content-options'
	)
);
/* EOF */