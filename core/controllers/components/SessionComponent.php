<?php
/**
* PockyPHP v1.0.0
* Copyright 2014, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

session_start();

class SessionComponent extends ModComponent {
	
	function write($name, $value = '') {
		$_SESSION[$name] = $value;
	}
	
	function check($name) {
		return isset($_SESSION[$name]);
	}
	
	function read($name) {
		if ($this->check($name)) {
			return $_SESSION[$name];
		} else {
			return false;
		}
	}
	
	function delete($name) {
		unset($_SESSION[$name]);
	}
	
	function setFlash($msg) {
		$_SESSION['flashMessage'] = $msg;
	}
}
