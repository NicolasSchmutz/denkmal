<?php

require_once 'Grabber/Location/Myspace.php';

/**
 * Grabber for NT Areal
 *
 */
class Grabber_Location_Myspace_Ntareal extends Grabber_Location_Myspace {

	/**
	 * Set up grabber
	 */
	function __construct() {
		$this->_location = Location::getLocation('NT Areal');
	}

	/**
	 * Return the myspace-profile id
	 * @return string Profile-id
	 */
	protected function _getProfileId() {
		return '198944946';
	}
}
