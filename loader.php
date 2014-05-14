<?php
/**
* PockyPHP v1.0.0
* Copyright 2014, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

//BASE and WEBROOT are set by webroot index.php
require_once(BASE. '/core/pocky_app.php');
$theApp = new PockyApp();

//identify mod by subdomain
$hostParts = explode('.', $_SERVER['HTTP_HOST']);
while (count($hostParts) < 3) array_unshift($hostParts, 'www');
$theApp->data['modName'] = $hostParts[count($hostParts) - 3];

set_include_path('.'. PATH_SEPARATOR. 
	BASE. '/mods/'. $theApp->data['modName']. PATH_SEPARATOR.
	BASE. '/app'. PATH_SEPARATOR. 
	BASE. '/core'
);

$appSettings = array();
require_once(BASE. '/config.php');
require_once('app_config.php');
require_once('mod_config.php');
$theApp->settings = $appSettings;

require_once('models/model.php');
require_once('models/app_model.php');
require_once('models/mod_model.php');
foreach($theApp->settings['Models'] as $model) {
	@include('models/'. $model. '.php');
	if (!class_exists($model)) {
		eval('class '. $model. ' extends ModModel{}');
	}
}
$theApp->loadModels();

require_once('controllers/controller.php');
require_once('controllers/app_controller.php');
require_once('controllers/mod_controller.php');
require_once('controllers/components/component.php');
require_once('controllers/components/app_component.php');
require_once('controllers/components/mod_component.php');
require_once('views/view.php');
require_once('views/helpers/helper.php');
require_once('views/helpers/app_helper.php');
require_once('views/helpers/mod_helper.php');


if (empty($_GET['url'])) $_GET['url'] = '/';
foreach($theApp->settings['Routes'] as $route => $resolve) {
	if ($_GET['url'] == $route) {
		$_GET['url'] = $resolve;
		break;
	}
}

$_GET['url'] = trim($_GET['url'], '/');
$urlPieces = explode('/', $_GET['url']);
foreach($theApp->settings['prefixes'] as $prefix) {
	if ($urlPieces[0] == $prefix) {
		$theApp->data['prefix'] = array_shift($urlPieces);
		if (empty($urlPieces[1])) $urlPieces[1] = 'index';
		break;
	}
}
if (empty($urlPieces) || empty($urlPieces[0])) {
	die('Error: No controller specified');
}
if (empty($urlPieces[1])) $urlPieces[1] = 'index';
$theApp->controllerName = PockyApp::classCase($urlPieces[0]);
$theApp->actionName = PockyApp::camelCase($urlPieces[1]);
if (!empty($theApp->data['prefix'])) {
	$theApp->actionName = $theApp->data['prefix']. '_'. $theApp->actionName;
}

require_once('controllers/'. $theApp->controllerName. '.php');
eval('$theApp->controller = new '. $theApp->controllerName. 'Controller();');
$theApp->controller->_addModels($theApp->models);
$theApp->controller->beforeAction();
call_user_func_array(
	array($theApp->controller, $theApp->actionName),
	array_slice($urlPieces, 2)
);

if ($theApp->controller->autoRender) {
	$theApp->controller->render();
}
echo $theApp->controller->output;
