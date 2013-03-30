<?php



/**
 * Ferien controller
 */
class FerienController extends Zend_Controller_Action
{
	
	public function init()
	{
		$this->_helper->layout->disableLayout();
	}
	
	
	/**
	 * Ferien page
	 */
	public function indexAction() {
		$this->view->days = $this->_getParam('days');
		$this->view->headTitle('DENKMAL.ORG');
	}
	
}
