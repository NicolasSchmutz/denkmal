<?php

ini_set("display_errors", "Off");
error_reporting(E_ALL | E_STRICT);
mb_internal_encoding('UTF-8');

$includePaths = array(
	'../',
	'../classes/',
	'../application/default/models/',
	'../application/default/views/helpers/',
	'../application/admin/models/',
	'../application/admin/views/helpers/',
	get_include_path()
);
set_include_path(implode(PATH_SEPARATOR, $includePaths));

require_once '../vendor/autoload.php';

require_once '../config/Config.php';
$config = new Config();
Zend_Registry::set('config', $config);

// Timezone
date_default_timezone_set('Europe/Zurich');

// Set debug-mode if desired
if ($config->debug) {
	ini_set("display_errors", "On");
}

// Include custom action-helpers
Zend_Controller_Action_HelperBroker::addPrefix('Denkmal_Action_Helper');

// Set doctype
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
/** @var $router Zend_Controller_Router_Rewrite */
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
