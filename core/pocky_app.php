<?php
class PockyApp {
	var $data = array();
	var $settings = array();
	
	var $controllerName;
	var $controller;
	var $actionName;
	
	public $dbConnections = array();
	public $models = array();
	function loadModels() {
		foreach($this->settings['Databases'] as $dbName => $dbInfo) {
			if ($dbInfo['type'] == 'mysqli') {
				$this->dbConnections[$dbName] = new mysqli($dbInfo['host'], $dbInfo['username'], $dbInfo['password'], $dbInfo['db_name']);
				if (mysqli_connect_error()) {
					die('Could not connect to database "'. $dbName. '".');
				}
			}
		}
		foreach($this->settings['Models'] as $model) {
			$m = new $model();
			$m->_connect($this->dbConnections[$m->connectionName]);
			$this->models[$model] = $m;
		}
	}
	
	
	static function filterForName($string) {
		return preg_replace('/[^a-z0-9_]/i', '', $string);
	}
	static function camelCase($string) {
		$string = PockyApp::filterForName($string);
		return preg_replace('/_[a-z]/e', "strtoupper(substr('$0', 1))", $string);
	}
	static function deCamelCase($string) {
		$string = PockyApp::filterForName($string);
		$string = strtolower(substr($string, 0, 1)). substr($string, 1);
		return preg_replace('/[A-Z]/e', "'_'. strtolower('$0')", $string);
	}
	static function classCase($string) {
		$string = PockyApp::camelCase(PockyApp::filterForName($string));
		return strtoupper(substr($string, 0, 1)). substr($string, 1);
	}
}
