<?php 

require_once 'Grabber/Location/Myspace.php';


/**
 * Grabber for Nordstern
 *
 */ 
class Grabber_Location_Myspace_Nordstern extends Grabber_Location_Myspace
{
	/**
	 * Set up grabber
	 */
	function __construct() {
		$this->_location = Location::getLocation('Nordstern');
	}
	/**
	 * Return the myspace-profile id
	 * @return string Profile-id
	 */
	protected function _getProfileId() {
		return '200687083';
	}
} 
