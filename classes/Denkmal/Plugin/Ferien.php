<?php



/**
 * Plugin to redirect end users in holiday times
 */
class Denkmal_Plugin_Ferien extends Zend_Controller_Plugin_Abstract
{
	private $_auth;

	// Redirect for holidays
	private $_redir = array('module' => 'default',
							'controller' => 'ferien',
							'action' => 'index');



	/**
	 * Called by the Dispatcher.
	 *
	 * @param Zend_Controller_Request_Abstract $request Current request
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		// Get current route
		$action = $request->action;
		$controller = $request->controller;
		$module = $request->module;

		if ($module == 'admin') {
			return;
		}
		if ($module == 'default' && $controller == 'add') {
			return;
		}

		if (!(@$ferienEnd = Zend_Registry::get('config')->ferienEnd)) {
			return;
		}

		$now = Zend_Date::now();
		if ($now->isEarlier($ferienEnd)) {
			if ($module == 'widget') {
				exit;
			}

			$diff = $ferienEnd->sub($now);
			$request->setParam('days', ceil($diff/60/60/24));
			$request->setModuleName($this->_redir['module']);
			$request->setControllerName($this->_redir['controller']);
			$request->setActionName($this->_redir['action']);
		}
	}

}
