<?php
/**
* PockyPHP v1.0.0
* Copyright 2014, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/
class PockyController {
	public $layout = 'default';
	public $autoRender = true;
	public $output = '';
	public $components = array();
	public $helpers = array();
	protected $viewData = array();
	protected $data = array();
	
	function __construct() {
		if (!in_array('Session', $this->components)) $this->components[] = 'Session';
		if (!in_array('Session', $this->helpers)) $this->helpers[] = 'Session';
		if (!in_array('Form', $this->helpers)) $this->helpers[] = 'Form';
		
		foreach($this->components as $component) {
			$className = PockyApp::classCase($component). 'Component';
			require_once('controllers/components/'. $className. '.php');
			$this->$component = new $className($this);
		}
		if (!empty($_POST['data'])) { $this->data = $_POST['data']; }
	}
	
	function _addModels($models) {
		foreach($models as $modelName => $obj) {
			$this->$modelName = $obj;
		}
	}
	
	function set($varName, $varValue) {
		$this->viewData[$varName] = $varValue;
	}
	
	function beforeAction() {}
	
	function beforeRender() {}
	
	function render($viewFile=NULL, $layout='default') {
		global $theApp;
		if (empty($viewFile)) {
			$viewFile = PockyApp::deCamelCase($theApp->controllerName). '/'.
				PockyApp::deCamelCase($theApp->actionName);
		}
		if (func_num_args() < 2) {
			$layout = $theApp->controller->layout;
		}
		
		$view = new PockyView();
		$view->data = $this->data;
		$view->loadHelpers($this->helpers);
		
		if (!isset($this->viewData['pageTitle'])) {
			$this->viewData['pageTitle'] = $theApp->controllerName;
		}
		$this->beforeRender();
		$this->output = $view->render($viewFile, $this->viewData, $layout);
		$this->afterRender();
		$this->autoRender = false;
	}
	
	function afterRender() {}
	
	function redirect($url) {
		header('Location: '. $url);
		exit;
	}
	
	function flash($msg, $redirect, $delay = 3) {
		global $theApp;
		$view = new PockyView();
		$viewData = array(
			'msg' => $msg,
			'redirect' => $redirect,
			'delay' => $delay,
			'pageTitle' => $theApp->controllerName
		);
		echo $view->render('flash', $viewData, '');
		exit;
	}
	
	function __call($name, $arguments) {
		die('Error: Could not find function '. get_class($this). '::'. $name. '() ');
	}
}

?>