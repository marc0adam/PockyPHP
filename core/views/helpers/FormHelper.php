<?php
class FormHelper extends ModHelper {
	
	function text($dataName, array $params = array()) {
		$modelName = '0';
		$fieldName = $dataName;
		if (strpos($dataName, '.') > 0) {
			list($modelName, $fieldName) = explode('.', $dataName, 2);
		} else {
			$params['name'] = $dataName;
		}
		
		$params['id'] = PockyApp::camelCase(str_replace('.', '_', $dataName));
		if (!isset($params['type'])) $params['type'] = 'text';
		if (!isset($params['name'])) {
			$params['name'] = 'data['. $modelName. ']['. $fieldName. ']';
		}
		if (!isset($params['value'])) {
			if (isset($this->view->data[$modelName][$fieldName])) {
				$params['value'] = $this->view->data[$modelName][$fieldName];
			}
		}
		
		echo '<input ';
		foreach($params as $key => $value) {
			echo $key. '="'. addslashes($value). '" ';
		}
		echo '/>';
	}
	function password($dataName, array $params = array()) {
		$params['type'] = 'password';
		$params['value'] = '';
		$this->text($dataName, $params);
	}
	
	function select() {
		
	}
	
	
	
}
