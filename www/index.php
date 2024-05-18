<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

$url = filter_var($_SERVER['REDIRECT_URL'] ?? '/', FILTER_SANITIZE_URL);
$url = str_replace('../', '/', $url);

require_once(__DIR__.'/../_app.php');
require_once(ROOT.'/routes.php');

// No page was found
header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
$ext = substr($url, strrpos($url, '.') ?: 1000);
if ($ext == '' || $ext == '.html') {
    echo '<h1>Sorry, we couldn\'t find that page.</h1>';
}
