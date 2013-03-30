<?php

/**
 * View-Helper to display json-data
 *
 */
class Zend_View_Helper_JsonData extends Zend_Controller_Action_Helper_Abstract {

	/**
	 * Return a json string
	 *
	 * @param array $array The data
	 * @return string The json representation of the data
	 */
	public function jsonData($data) {
		if ($data) {
			return json_encode($data);
		} else {
			return '{}';
		}
	}
}
