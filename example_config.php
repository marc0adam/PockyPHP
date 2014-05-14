<?php
/**
* PockyPHP v1.0.0
* Copyright 2014, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

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
