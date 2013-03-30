<?php


/**
 * Plugin to determine action-context
 *
 */
class Denkmal_Plugin_Context extends Zend_Controller_Plugin_Abstract {

	private $_contextSwitchHelper = null;

	/**
	 * Called by the Dispatcher
	 *
	 * @param Zend_Controller_Request_Abstract $request Current request
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$this->_getContextSwitchHelper()->setContext('mobile', array('suffix' => 'mobile'));

		if ($this->_isMobile($request)) {
			$request->setParam('format', 'mobile');
		}
		if ($request->isXmlHttpRequest()) {
			$this->_getContextSwitchHelper()->setAutoJsonSerialization(false);
			$request->setParam('format', 'json');
		}

		if ($this->_requestContains($request, 'Android')) {
			$request->setParam('device', 'android');
		}
	}

	/**
	 * Decide whether a request comes from a mobile device
	 *
	 * @param Zend_Controller_Request_Abstract $request Request
	 */
	private function _isMobile(Zend_Controller_Request_Abstract $request) {
		return $this->_requestContains($request, array('iPhone', 'iPod', 'Mobile Safari'));
	}

	/**
	 * Check whether a request's user-agent header contains one of the given string
	 *
	 * @param Zend_Controller_Request_Abstract $request The request
	 * @param array                            $needles An array of string to look for
	 * @return boolean Returns TRUE, if at least one string in $needles is found in the user-agent
	 */
	private function _requestContains(Zend_Controller_Request_Abstract $request, $needles) {
		$needles = (array) $needles;
		$ua = $request->getHeader('USER_AGENT');
		foreach ($needles as $needle) {
			if (stripos($ua, $needle) !== false) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Retrieve ContextSwitch
	 *
	 * @return Zend_Controller_Action_Helper_ContextSwitch ContextSwitch-helper
	 */
	private function _getContextSwitchHelper() {
		if (null === $this->_contextSwitchHelper) {
			$this->_contextSwitchHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch');
		}
		return $this->_contextSwitchHelper;
	}
}
