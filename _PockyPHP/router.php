<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

/**
 * If the given $url matches the given $route, execute the given $action.
 * @param string $url  The URL being evaluated
 * @param string $route  A string to describe the route.
 *          Route segments (alphanumeric strings separated by slashes) can be placeholders for the value of the $url
 *          Any segments beginning with a colon will be passed as a named parameter to the $action. If the segment
 *              contains any non-variable characters (besides the starting colon), the rest of the segment will be
 *              used as a Regex expression to validate the data.
 *          Any segments enclosed in curly braces will be treated as an array element for the $action array. The field
 *              will be checked to confirm only alpha-numeric characters are present.
 * @param mixed $action  The action to take if the $route matches the $url.
 *          If $action is a function, it will be called with the named parameters.
 *          If $action is an array, it may contain keys for 'controller' and 'function'.
 *
 * Example Routes:
 *     route($url, '/users', ['controller' => 'UsersController', 'function' => 'index']);
 *     route($url, '/users/:id[0-9]+', ['controller' => 'UsersController', 'function' => 'view' ]);
 *     route($url, '/users/:id[0-9]+/edit', ['controller' => 'UsersController', 'function' => 'edit' ]);
 *     route($url, '/users/{function}', ['controller' => 'UsersController', ]);
 *     route($url, '/logout', function() {
 *         $u = new UsersController();
 *         $u->logout();
 *     });
 *     route($url, /{controller}/{function});
 * 
 **/
function route(string $url, string $route, $action = []) {
    $url_pieces   = explode('/', trim($url,'/'));
    $route_pieces = explode('/', trim($route,'/'));
    if (count($url_pieces) != count($route_pieces)) return;

    $passed_params = [];
    $url_action = [
        'function' => 'index'
    ];
    foreach($route_pieces as $i => $segment) {
        if ($segment == '{controller}' && preg_match('/^[a-z][a-z0-9_]*$/i', $url_pieces[$i])) {
            $url_action['controller'] = $url_pieces[$i];
        } elseif ($segment == '{function}' && preg_match('/^[a-z][a-z0-9_]*$/i', $url_pieces[$i])) {
            $url_action['function'] = $url_pieces[$i];
        } elseif (preg_match('/^:([a-z][a-z0-9_]*)(.*)$/i', $segment, $matches)) {
            if (!empty($matches[2])) {
                $regex = '/^'. $matches[2]. '$/';
                if (!preg_match($regex, $url_pieces[$i])) {
                    // URL doesn't match user's regex
                    return;
                }
            }
            $passed_params[$matches[1]] = $url_pieces[$i];
        } elseif ($segment != $url_pieces[$i]) {
            // segment does not match url
            return;
        }
    }

    if (is_callable($action)) {
        try {
            call_user_func_array($action, $passed_params);
        } catch (Exception $e) {
            error_log("Error calling action for Route $route from URL $url");
            return;
        }
    } elseif (is_array($action)) {
        $action = array_merge($url_action, $action);
        if (empty($action['controller'])) {
            error_log("Error: Route $route provides no controller");
            return;
        }
        if (substr($action['controller'], -10) != 'Controller') {
            $action['controller'] .= 'Controller';
        }
        $controllerName = $action['controller'];
        $functionName = $action['function'] ?? 'index';
        if (!class_exists($controllerName)) {
            error_log("Error: Route $route requests an unknown Controller: $controllerName");
            return;
        }
        if (!method_exists($controllerName, $functionName)) {
            // error_log("Error: Route $route requests an unknown Function: $controllerName :: $functionName");
            return;
        }
        try {
            echo call_user_func_array([new $controllerName(), $functionName], $passed_params);
            exit;
        } catch (Exception $e) {
            error_log("Error calling Route $route action $controllerName :: $functionName");
            return;
        }
    } elseif (is_string($action)) {
        header('Location: '. $action);
        exit;
    } else {
        error_log("Error: Route $route calls an invalid action: ". json_encode($action));
        return;
    }
    
}
