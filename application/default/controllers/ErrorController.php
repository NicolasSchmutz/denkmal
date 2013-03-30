<?php



/**
 * Error controller
 *
 */
class ErrorController extends Zend_Controller_Action
{
	/**
	 * Error page
	 */
	public function errorAction()
	{
		$errors = $this->_getParam('error_handler');
		$config = Zend_Registry::get('config');

		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				$this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
				$this->view->msg = 'Not found';
				break;
			default:
				if ($config->debug) {
					$this->view->msg = $errors->exception->getMessage();
					$this->view->stack .= $errors->exception->getTraceAsString();
				} else {
					$this->view->msg = 'Application error (sorry)';
				}
				break;
        }

        $this->getResponse()->clearBody();
        $this->view->headTitle('DENKMAL.ORG Eventkalender', Zend_View_Helper_Placeholder_Container_Abstract::SET);
	}
}
