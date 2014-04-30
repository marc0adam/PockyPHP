<?php
/**
* PockyPHP
* Copyright 2014, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/
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
	
	/*
	* @param array $params The HTML attributes for the select element.
	*		Additional $params:
	*			'empty' => false: Do not add an empty <option>
	*			'empty' => true: Add an empty <option> with empty value and text
	*			'empty' => string: Use the string as the text for empty value <option>
	*/
	function select($dataName, $choices = array(), $selected = NULL, $params = array()) {
		$modelName = '0';
		$fieldName = $dataName;
		if (strpos($dataName, '.') > 0) {
			list($modelName, $fieldName) = explode('.', $dataName, 2);
		} else {
			$params['name'] = $dataName;
		}
		
		$params['id'] = PockyApp::camelCase(str_replace('.', '_', $dataName));
		if (!isset($params['name'])) {
			$params['name'] = 'data['. $modelName. ']['. $fieldName. ']';
		}
		if ($selected == NULL) {
			if (isset($this->view->data[$modelName][$fieldName])) {
				$selected = $this->view->data[$modelName][$fieldName];
			}
		}
		$opts = array();
		if (isset($params['empty'])) {
			if (is_string($params['empty'])) {
				$opts[] = '<option value="">'. $params['empty']. '</option>';
			} elseif ($params['empty'] == true) {
				$opts[] = '<option value=""></option>';
			} else { // 'empty' == false
				//do nothing
			}
			unset($params['empty']);
		}
		foreach($choices as $value => $text) {
			$opts[] = '<option value="'. $value. '">'. $text. '</option>';
		}
		
		echo '<select ';
		foreach($params as $key => $value) {
			echo $key. '="'. addslashes($value). '" ';
		}
		echo ">\n";
		echo implode("\n", $opts);
		echo "</select>\n";
	}
	
	
	function textarea($dataName, array $params = array()) {
		$modelName = '0';
		$fieldName = $dataName;
		if (strpos($dataName, '.') > 0) {
			list($modelName, $fieldName) = explode('.', $dataName, 2);
		} else {
			$params['name'] = $dataName;
		}
		
		$params['id'] = PockyApp::camelCase(str_replace('.', '_', $dataName));
		if (!isset($params['name'])) {
			$params['name'] = 'data['. $modelName. ']['. $fieldName. ']';
		}
		$value = (isset($this->view->data[$modelName][$fieldName])) ? $this->view->data[$modelName][$fieldName] : '';
		
		echo '<textarea ';
		foreach($params as $key => $value) {
			echo $key. '="'. addslashes($value). '" ';
		}
		echo '>'. $value. '</textarea>';
	}
	
}
