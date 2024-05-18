<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

// Load everything needed by the app.
function env($key) { return $_ENV[$key] ?? null; }

define('ROOT', __DIR__);
require_once(ROOT.'/.env.php');
require_once(ROOT.'/_PockyPHP/autoloader.php');
require_once(ROOT.'/_PockyPHP/functions.php');
require_once(ROOT.'/_PockyPHP/CustomSessionHandler.php');
