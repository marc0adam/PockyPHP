<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

$_app_db = new MySqlDatabaseService([
    'host'     => env('DB_HOST'),
    'user'     => env('DB_USER'),
    'password' => env('DB_PASSWORD'),
    'database' => env('DB_DATABASE'),
]);
function query($sql, $values = []) { global $_app_db; return $_app_db->query($sql, $values); }
function querySingleResult($sql, $values = []) { global $_app_db; return $_app_db->querySingleResult($sql, $values); }
function lastInsertId() { global $_app_db; return $_app_db->lastInsertId(); }
function rowsAffected() { global $_app_db; return $_app_db->rowsAffected(); }

