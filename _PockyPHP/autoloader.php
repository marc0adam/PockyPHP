<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

spl_autoload_register(function($class) {
    $namespace = substr($class, 0, strrpos($class, '\\'));
    $class_name = trim(substr($class, strlen($namespace)), '\\');
    $file_path = '';

    switch ($namespace) {
        case '':
            if (substr($class_name, -10)    === 'Controller') $file_path = 'Controllers/';
            elseif (substr($class_name, -5) === 'Model')      $file_path = 'Models/';
            elseif (substr($class_name, -7) === 'Service')    $file_path = 'Services/';
            break;
        case 'PockyPHP':
            $file_path = '_PockyPHP/';
            break;
    }
    $filename = ROOT.'/'.$file_path.$class_name.'.php';
    if (file_exists($filename)) {
        require_once($filename);
        return;
    }
});
