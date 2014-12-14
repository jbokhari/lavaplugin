<?php 
interface iLavaNotifier {
	public function add($msg, $type);
	public function display();
	public function has_messages();

}