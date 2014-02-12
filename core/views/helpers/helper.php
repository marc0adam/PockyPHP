<?php
class PockyHelper {
	public $view;
	
	function __construct(&$view) {
		$this->view =& $view;
	}
}
