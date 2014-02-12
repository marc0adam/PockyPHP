<?php
/**
* PockyPHP
* Copyright 2014, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/
class PockyModel {
	protected $dbFormat = 'mysqli';
	protected $tableName = '';
	public $connectionName = 'default';
	protected $dbCn;
	
	function __construct() {
		if (empty($this->tableName)) {
			$this->tableName = PockyApp::deCamelCase(get_class($this)). 's';
			if (substr($this->tableName, -2) == 'ys') {
				$this->tableName = substr($this->tableName, 0, strlen($this->tableName)-2). 'ies';
			}
		}
	}
	
	function _connect($dbConnection) {
		$this->dbCn = $dbConnection;
	}
	
	public $recursive = 0;
	public $belongsTo = array();
	/*
	$belongsTo = array(
		'ModelName' => array(
			'className' => 'Table',
			'foreignKey' => 'table_id'
		)
	);
	*/
	public $hasOne = array();
	public $hasMany = array();
	public $hasAndBelongsToMany = array();
	/*
	*	string $type choices include first, all, count, NOT list
	*	array $options array indexes can include:
	*		fields,
	*		conditions,
	*		group,
	*		order,
	*		limit
	*/
	function find($type, $options = array()) {
		global $theApp;
		if ($type == 'first') {
			$options['limit'] = 1;
		}
		if ($type == 'count') {
			$sql = 'Select Count(*) as `count` ';
		} elseif (!empty($options['fields'])) {
			$sql = 'Select `'. implode('`, `', $options['fields']). '` ';
		} else {
			$sql = 'Select * ';
		}
		
		$sql .= 'From `'. $this->tableName. '` as `'. get_class($this). '` ';
		if ($this->recursive >= 0) {
			foreach($this->belongsTo as $joinName => $joinData) {
				$sql .= 'Left Join `'. $theApp->models[$joinData['className']]->tableName. '` as `'. $joinName. '` On `'. get_class($this). '`.`'. $joinData['foreignKey']. '`=`'. $joinName. '`.`id` ';
			}
			foreach($this->hasOne as $joinName => $joinData) {
				$sql .= 'Left Join `'. $theApp->models[$joinData['className']]->tableName. '` as `'. $joinName. '` On `'. get_class($this). '`.`id`=`'. $joinName. '`.`'. $joinData['foreignKey']. '` ';
			}
		}
		
		if (!empty($options['conditions'])) {
			$sql .= 'Where '. implode(' And ', $this->buildConditions($options['conditions'])). ' ';
		}
		if (!empty($options['group'])) {
			$sql .= 'Group By '. $options['group']. ' ';
		}
		if (!empty($options['order'])) {
			$sql .= 'Order By '. $options['order']. ' ';
		}
		if (!empty($options['limit'])) {
			$sql .= 'Limit '. $options['limit'];
		}
		if ($type == 'count') {
			$tmp = $this->query($sql);
			return $tmp[0][0]['count'];
		} else {
			$tmp = $this->query($sql);
			if ($type == 'first' && is_array($tmp)) $tmp = array(0=>$tmp[0]);
			if ($this->recursive >= 1) {
				foreach($this->hasMany as $joinName => $joinData) {
					$foreignModel = $theApp->models[$joinData['className']];
					for ($i=0; $i < count($tmp); $i++) {
						$tmp[$i][$joinName] = $foreignModel->find('all', array(
							'conditions' => array(
								$joinData['foreignKey'] => $tmp[$i][get_class($this)]['id']
							),
							'order' => $joinData['order']
						));
					}
				}
				foreach($this->hasAndBelongsToMany as $joinName => $joinData) {
					$foreignModel = $theApp->models[$joinData['className']];
					for ($i=0; $i < count($tmp); $i++) {
						$tmp[$i][$joinName] = $foreignModel->query(sprintf(
							"Select `%s`.* From `%s` `%s` ".
								"Left Join `%s` `_temp` On `%s`.`id`=`_temp`.`%s` ".
							"Where `_temp`.`%s`='%s' ".
							"Order By `%s`",
							$joinData['className'], $foreignModel->tableName,
							$joinData['className'], $joinData['joinTable'],
							$joinData['className'], $joinData['associationForeignKey'],
							$joinData['foreignKey'], $tmp[$i][get_class($this)]['id'],
							(empty($joinData['order'])) ? 'id' : $joinData['order']
						));
					}
				}
			}
			if ($type == 'first' && is_array($tmp)) return $tmp[0];
			else return $tmp;
		}
	}
	function buildConditions($conditions) {
		$resp = array();
		foreach($conditions as $key => $value) {
			if (is_array($value)) {
				if (trim(strtolower($key)) == 'or') { //OR
					$resp[] = '('. implode(' Or ', $this->buildConditions($value)). ')';
				} else { //IN
					$tmp = array();
					foreach($value as $val) {
						$tmp[] = $this->dbCn->real_escape_string($val);
					}
					$resp[] = "`". $key. "` In ('". implode("', '", $tmp). "')";
				}
			} elseif(strpos($key, '=') !== false ||
					strpos(strtolower($key), ' in') !== false ||
					strpos(strtolower($key), ' like') !== false
			) {
				//=, !=, IN, LIKE, NOT LIKE
				$resp[] = $key. " '". $this->dbCn->real_escape_string($value). "'";
			} else {
				$resp[] = '`'. implode('`.`', explode('.', $key)). "`='".
					$this->dbCn->real_escape_string($value). "'";
			}
		}
		return $resp;
	}
	
	public $id;
	function save($data) {
		if (isset($data[get_class($this)])) {
			$data = $data[get_class($this)];
		}
		
		$cols = array();
		$tmpCols = $this->query('show columns from '. $this->tableName);
		foreach($tmpCols as $tmp) {
			$cols[] = $tmp['COLUMNS']['Field'];
		}
		foreach($data as $key => $value) {
			if (!in_array($key, $cols)) unset($data[$key]);
		}
		
		if (isset($data['id'])) { //update query
			$sql = 'Update `'. $this->tableName. '` Set ';
			$parts = array();
			foreach($data as $col => $value) {
				$parts[] = "`". $col. "`='". $this->dbCn->real_escape_string($value). "'";
			}
			$sql .= implode(', ', $parts);
			$sql .= " Where `id`='". $this->dbCn->real_escape_string($data['id']). "' ".
				"Limit 1";
			$ret = $this->query($sql);
			if ($ret === true) {
				$this->id = $data['id'];
			} else {
				$this-> id = 0;
			}
			return $ret;
			
		} else { //insert query
			$parts_a = array();
			$parts_b = array();
			foreach($data as $col => $value) {
				$parts_a[] = $col;
				$parts_b[] = $this->dbCn->real_escape_string($value);
			}
			$sql = "Insert Into `". $this->tableName. "` (`".
				implode("`, `", $parts_a). "`) Values ('".
				implode("', '", $parts_b). "')";
			$ret = $this->query($sql);
			if ($ret === true) {
				$this->id = $this->dbCn->insert_id;
			} else {
				$this->id = 0;
			}
			return $ret;
		}
		
	}
	
	function delete($id) {
		$sql = "Delete From `". $this->tableName. "` Where id='".
			$this->dbCn->real_escape_string($id). "' Limit 1";
		return $this->query($sql);
	}
	
	function deleteAll($conditions) {
		$sql = "Delete From `". $this->tableName. "` Where ".
			implode(' And ', $this->buildConditions($conditions));
		return $this->query($sql);
	}
	
	function __call($name, $arguments) {
		if (substr($name, 0, 6) == 'findBy') {
			return $this->find('first', array('conditions' => array(
				get_class($this). '.'. PockyApp::deCamelCase(substr($name, 6)) => $arguments[0]
			)));
		} elseif (substr($name, 0, 9) == 'findAllBy') {
			return $this->find('all', array('conditions' => array(
				get_class($this). '.'. PockyApp::deCamelCase(substr($name, 9)) => $arguments[0]
			)));
		} else {
			echo 'Error: Unknown function requested. '. get_class($this). '::'. $name. '() ';
		}
	}
	
	function query($sql) {
		$rs = $this->dbCn->query($sql, MYSQLI_STORE_RESULT);
		if ($rs === true) return true;
		if ($rs === false) {
			echo $this->dbCn->error;
			return $rs;
		}
		$response = array();
		$fields = $rs->fetch_fields();
		for ($i=0; $i < count($fields); $i++) {
			if ($fields[$i]->table == '') $fields[$i]->table = 0;
		}
		$rowCounter = 0;
		while ($row = $rs->fetch_row()) {
			for ($i=0; $i < count($fields); $i++) {
				$response[$rowCounter][$fields[$i]->table][$fields[$i]->name] = $row[$i];
			}
			$rowCounter++;
		}
		return $response;
	}
	
}
?>