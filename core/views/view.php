<?php
/**
* PockyPHP v1.0.0
* Copyright 2014, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/
class PockyView {
	public $data = array();
	
	function loadHelpers($helpers) {
		foreach($helpers as $helper) {
			$className = PockyApp::classCase($helper). 'Helper';
			require_once('views/helpers/'. $className. '.php');
			$this->$helper = new $className($this);
		}
	}
	
	function render($viewFile, $viewData, $layout) {
		extract($viewData);
		ob_start();
		require_once('views/'. $viewFile. '.ctp');
		$pageContent = ob_get_contents();
		ob_clean();
		if (!empty($layout)) {
			require_once('views/'. $layout. '.ctp');
			$output = ob_get_contents();
		} else {
			$output = $pageContent;
		}
		ob_end_clean();
		return $output;
	}
	
}
