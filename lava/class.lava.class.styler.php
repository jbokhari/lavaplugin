<?php 
class LavaClassStyler {
	public $classes = array();
	public function __construct( array $types = array("label_classes", "container_classes", "outter_classes", "field_classes") ){
		foreach ($types as $type) {
			$this->classes[$type] = array();
		}
		print_r($this);
	}
	public function add_container_class($class){
		$this->add_class($class, "container_classes");
	}
	public function add_label_class($class){
		$this->add_class($class, "label_classes");
	}
	public function add_outer_class($class){
		$this->add_class($class, "outer_classes");
	}
	public function input_classes(){
		echo $this->get_classes_list("classes");
	}
	/**
	 * 
	 * Get all classes by name $ref
	 * 
	 **/
	public function get_classes_list($ref = "classes"){
		$classes = implode( " ", $this->$clases[$ref] );
		return $classes;
	}
	/**
	 * 
	 * Add Class, adds class based on reference
	 * accpets both array of classes, space separated classes or single class string
	 *
	 **/
	public function add_class($class, $ref = "classes"){
		if (is_array($class))
			$this->$clases[$ref] = array_merge($this->$clases[$ref], $class);
		else array_push($this->$clases[$ref], $class);
	}
	public function get_label_html_classes(){
		return $this->get_classes_list("label_classes");
	}
	public function get_container_html_classes(){
		return $this->get_classes_list("container_classes");
	}
	public function get_outer_html_classes(){
		return $this->get_classes_list("outer_classes");
	}
}