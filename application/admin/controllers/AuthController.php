<?php

require_once 'Auth.php';


/**
 * Auth controller
 *
 */
class Admin_AuthController extends Zend_Controller_Action
{
	
	public function init()
	{			
		$this->view->headTitle('DENKMAL.ORG Admin');
	}
	
	
	/**
	 * Login Action
	 */
	function loginAction() {
		if ($this->_request->isPost()) {
			// This is a login-attempt
			$user = $this->_getParam('user');
			$pass = $this->_getParam('pass');
			
			try {
				// Login
				Auth::login($user, $pass);
				$this->_redirect('/admin/');
			} catch(Denkmal_Exception $e) {
				$this->view->error = $e->getMessage();
				$this->view->user = $user;
			}
		}
		
		$this->view->hideLogout = true;
	}

	
	/**
	 * Logout Action
	 */
	function logoutAction() {
		Auth::logout();
		$this->_redirect('/admin/');
	}
	

}
