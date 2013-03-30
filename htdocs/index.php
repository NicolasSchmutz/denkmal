<?php

/**
 * Bootstrap-Script
 */

ini_set("display_errors","Off");

error_reporting(E_ALL|E_STRICT); 

mb_internal_encoding('UTF-8');

$path = array(
	'../library/',
	'../config/',
	'../classes/',
	'../application/default/models/',
	'../application/default/views/helpers/',
 	'../application/admin/models/',
	'../application/admin/views/helpers/',
	get_include_path()
	);
set_include_path(implode(PATH_SEPARATOR, $path));


require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Controller/Router/Route/Static.php';
require_once 'Zend/Controller/Router/Route/Regex.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Layout.php';
require_once 'Zend/Session.php';
require_once 'Denkmal/Plugin/Context.php';
require_once 'Denkmal/Plugin/Auth.php';
require_once 'Denkmal/Plugin/Ferien.php';
require_once 'Denkmal/Cache.php';

require_once 'Config.php';
$config = new Config();
Zend_Registry::set('config', $config);

// Timezone
date_default_timezone_set('Europe/Zurich');

// Set debug-mode if desired
if ($config->debug) {
	ini_set("display_errors","On");
}

// Include custom action-helpers
Zend_Controller_Action_HelperBroker::addPrefix('Denkmal_Action_Helper');

// Set doctype
require_once 'Zend/View/Helper/Doctype.php';
$doctypeHelper = new Zend_View_Helper_Doctype();
$doctypeHelper->doctype('XHTML1_STRICT');

// Layout
Zend_Layout::startMvc();

// Session
Zend_Session::start();

// Get front-controller 
$frontController = Zend_Controller_Front::getInstance();
$frontController->addModuleDirectory('../application/')
	->registerPlugin(new Denkmal_Plugin_Context())
	->registerPlugin(new Denkmal_Plugin_Auth())
	->registerPlugin(new Denkmal_Plugin_Ferien());

// Router
$router = $frontController->getRouter();
$router->addRoute('index',
	new Zend_Controller_Router_Route_Regex(
		'(mo|di|mi|do|fr|sa|so)(_(\d+))?',
		array('module' => 'default', 'controller' => 'index', 'action' => 'index')
	));
$router->addRoute('googleverification',
	new Zend_Controller_Router_Route_Static(
		'google650879864b506576.html',
		array('module' => 'default', 'controller' => 'index', 'action' => 'googleverification')
	));
		
// Run!
$frontController->dispatch();