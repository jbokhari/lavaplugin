<?php
abstract class LavaLogging22 {
	public $error = array();
	public $log = array();
	public function display_logs( $echo = true, $verbose = false ){
		$html  = "<h3>Logs:</h3>";
		$html .= "<ul>";
		foreach($this->log as $log){
			$html .= "<li>$log</li>";
		}
		$html .= "</ul>";
		if ($echo == true){
			echo $html;
		} else {
			return $html;
		}
	}
	public function display_errors($echo = true, $verbose = false){
		$count = count( $this->error );
		$html  = "";
		$html .= "<h3>Errors</h3>";
		$html .= "<ul>";
		foreach($this->error as $error){
			$html .= "<li>$error</li>";
		}
		$html .= "</ul>";
		if ($echo == true){
			echo $html;
		} else {
			return $html;
		}
	}
	public function _log($string){
		$this->log[] = $string;
	}
	public function _error($string){
		$this->error[] = $string;
	}
}