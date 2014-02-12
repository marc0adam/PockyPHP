<?php
class PockyController {
	public $layout = 'default';
	public $autoRender = true;
	public $output = '';
	public $helpers = array();
	protected $viewData = array();
	protected $data = array();
	
	function __construct() {
		if (!in_array('form', $this->helpers)) $this->helpers[] = 'form';
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
	
	function __call($name, $arguments) {
		die('Error: Could not find function '. get_class($this). '::'. $name. '() ');
	}
}

?>