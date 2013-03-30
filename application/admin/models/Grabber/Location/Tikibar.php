<?php

require_once 'Grabber/Location.php';

/**
 * Grabber for Tiki-Bar
 *
 */
class Grabber_Location_Tikibar extends Grabber_Location {

	/**
	 * Set up grabber
	 */
	function __construct() {
		$this->_location = Location::getLocation('Platanenhof');
	}

	/**
	 * Grab events
	 */
	protected function _grab() {
		$str = new Grabber_String('http://www.tiki-bar.ch/');

		$str->cut('/Rückblick.*$/');

		$str->replace('#<strong>(.*?)</strong>#', '$1', true);

		foreach ($str->matchAll('#\w{2}[,\.]{1,2}\s*(\d{1,2}).(\d{1,2}).\s+([^<]+)#') as $matches) {
			$this->_foundEvent($matches[0]);
			$description = new Grabber_String('Tiki Bar: ' . $matches[3]);
			$from = new Grabber_Date($matches[1], $matches[2]);
			$from->setTime(21);
			$this->_addEvent($description, $from);
		}
	}
}
