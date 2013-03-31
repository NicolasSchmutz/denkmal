<?php

require_once 'Grabber/Location.php';

/**
 * Grabber for Cafe Hammer
 *
 */
class Grabber_Location_Cafehammer extends Grabber_Location {

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

		$str->replace('#<br>#', "\n", true);

		$str->stripTags();

		foreach ($str->matchAll('#(\d{1,2})\.(\d{1,2})\.(\d{2,4})\s+[-–]\s+(?:ca\.?\s*)?(\d{1,2}):(\d{2})(.+?)\n\s*\n#us') as $match) {
			$this->_foundEvent($match[0]);
			$from = new Grabber_Date($match[1], $match[2], $match[3]);
			$from->setTime($match[4], $match[5]);
			$description = new Grabber_Description($match[6]);
			$this->_addEvent($description, $from);
		}
	}
}
