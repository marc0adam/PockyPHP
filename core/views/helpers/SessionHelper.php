<?php
/**
* PockyPHP v1.0.0
* Copyright 2014, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/
class SessionHelper extends ModHelper {
	
	function flash() {
		if (!empty($_SESSION['flashMessage'])) {
			echo '<div id="flashMessage">'. $_SESSION['flashMessage']. '</div>';
			unset($_SESSION['flashMessage']);
		}
	}

}
