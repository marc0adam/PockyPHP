<?php
class PockyDatabase {
	
	function connect() {
		mysql_connect('localhost', 'db_user', 'password') or die('Error: Cannot connect to database');
		mysql_select_db('pocky') or die('Error: Cannot open the database');
	}
	
	
	
}

?>