<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

require_once(ROOT.'/_PockyPHP/router.php');
$url = $url ?? '';

/**
 * Example routes:
 * 
route($url, '/users/:id[0-9]+/edit', ['controller' => 'UsersController', 'function' => 'edit' ]);
route($url, '/logout', function() {
    $u = new UsersController();
    $u->logout();
});
route($url, /{controller}/{function});
**/

route($url, '/', ['controller' => 'UsersController', 'function' => 'index']);
route($url, '/users/:id[0-9]+', ['controller' => 'UsersController', 'function' => 'view' ]);
route($url, '/users/{function}',  ['controller' => 'Users']);
