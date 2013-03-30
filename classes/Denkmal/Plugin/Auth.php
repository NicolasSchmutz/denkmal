<?php



/**
 * Plugin to authenticate users
 *
 * This plugin checks whether the current user (by session) is authenticated.
 */
class Denkmal_Plugin_Auth extends Zend_Controller_Plugin_Abstract {

	private $_auth;

	// Route for un-authenticated users
	private $_login = array('module'     => 'admin',
							'controller' => 'auth',
							'action'     => 'login');

	/**
	 * Constructor. Sets the Zend_Auth
	 *
	 */
	public function __construct() {
		$this->_auth = Zend_Auth::getInstance();
	}

	/**
	 * Called by the Dispatcher. Checks access-rules based on ACL
	 *
	 * @param Zend_Controller_Request_Abstract $request Current request
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$role = 'guest';

		// Get the current users role
		if ($this->_auth->hasIdentity()) {
			// User is authenticated
			if (@$this->_auth->getIdentity()->role) {
				$role = $this->_auth->getIdentity()->role;
			}
		}

		// Key login
		$loginKey = $this->getRequest()->getParam('loginkey');
		if ($loginKey == Zend_Registry::get('config')->loginkey) {
			$role = 'admin';
		}

		// Get current route
		$action = $request->action;
		$controller = $request->controller;
		$module = $request->module;

		if ($module == 'admin' && $role != 'admin') {
			// Reroute to login
			$module = $this->_login['module'];
			$controller = $this->_login['controller'];
			$action = $this->_login['action'];
		}

		if ($module != $request->module || $controller != $request->controller || $action != $request->action) {
			// Reroute user to new location
			$request->setModuleName($module);
			$request->setControllerName($controller);
			$request->setActionName($action);
		}
	}
}
