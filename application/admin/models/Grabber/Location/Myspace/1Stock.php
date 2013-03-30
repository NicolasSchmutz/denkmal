<?php 

require_once 'Grabber/Location/Myspace.php';


/**
 * Grabber for 1. Stock Schoolyard
 *
 */ 
class Grabber_Location_Myspace_1Stock extends Grabber_Location_Myspace
{
	/**
	 * Set up grabber
	 */
	function __construct() {
		$this->_location = Location::getLocation('1.Stock | Schoolyard');
	}
	/**
	 * Return the myspace-profile id
	 * @return string Profile-id
	 */
	protected function _getProfileId() {
		return '145091103';
	}
} 
