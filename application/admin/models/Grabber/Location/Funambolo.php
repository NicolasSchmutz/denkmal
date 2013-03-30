<?php

require_once 'Grabber/Location.php';

/**
 * Grabber for Funambolo
 *
 */
class Grabber_Location_Funambolo extends Grabber_Location {

	/**
	 * Set up grabber
	 */
	function __construct() {
		$this->_location = Location::getLocation('Funambolo');
	}

	/**
	 * Grab events
	 */
	protected function _grab() {
		$str = new Grabber_String('http://www.funambolo.ch/Programm.html');

		$str = $str->between('<h2>Programm:</h2>', '</body>');

		foreach ($str->matchAll('#<h3>\w+\.?\s+(\d+)\.\s+([\wöäü]+)\.?</h3>\s*<p>(.+?)</p>#') as $matches) {
			$this->_foundEvent($matches[0]);
			$description = new Grabber_String($matches[3]);
			$description->replace('<br>', '. ');
			$from = new Grabber_Date($matches[1], $matches[2]);
			$from->setTime($description->matchOne('#ab\s+(\d+)\s*Uhr#i', true));
			$this->_addEvent($description, $from);
		}
	}
}
