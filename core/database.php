<?php
/**
* PockyPHP
* Copyright 2014, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/
class PockyDatabase {
	
	function connect() {
		mysql_connect('localhost', 'db_user', 'password') or die('Error: Cannot connect to database');
		mysql_select_db('pocky') or die('Error: Cannot open the database');
	}
}
