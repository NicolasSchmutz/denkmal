<?php 

require_once 'Grabber/Location.php';


/**
 * Grabber for Cafe Hammer
 *
 */ 
class Grabber_Location_Cafehammer extends Grabber_Location
{
	/**
	 * Set up grabber
	 */
	function __construct() {
		$this->_location = Location::getLocation('Cafe Hammer');
	}
	
	/**
	 * Grab events
	 */
	protected function _grab() {
		$str = new Grabber_String('http://cafehammer.ch/');
					
		foreach ($str->matchAll('#<div id="eventdate">.+? (\d+) (\d+) (\d+) (\d+):(\d+)</div>\s*<div id="eventtitle">(.+?)</div>#') as $matches) {
			$this->_foundEvent($matches[0]);
			$from = new Grabber_Date($matches[1], $matches[2], $matches[3]);
			$from->setTime($matches[4], $matches[5]);
			$description = new Grabber_Description($matches[6]);
			$this->_addEvent($description, $from);
		}	

	}

}
