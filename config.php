<?php
$appSettings = array(
	'Databases' => array(
		'default' => array(
			'type' => 'mysqli',
			'host' => 'localhost',
			'username' => 'db_user',
			'password' => 'password',
			'db_name' => 'pocky'
		)
	),
	'Models' => array(
		'User', 'Post', 'Comment', 'Category'
	),
	
	'Routes' => array(
		'/' => '/users/'
	),
	
	'prefixes' => array('admin')
	
);
