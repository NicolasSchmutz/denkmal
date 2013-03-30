<?php

require_once 'Grabber/Location.php';

/**
 * Grabber for Hinterhof
 *
 */
class Grabber_Location_Hinterhof extends Grabber_Location {

	/**
	 * Set up grabber
	 */
	function __construct() {
		$this->_location = Location::getLocation('Hinterhof');
	}

	/**
	 * Grab events
	 */
	protected function _grab() {
		$str = new Grabber_String('http://hinterhof.ch/programm/');

		$str = $str->between('<ul id="events"', '<div id="footer"');

		foreach ($str->matchAll('#<div class="summary">\s*<div class="weekday">\w+ (\d+)\.(\d+) - (.+?)(?: - (.+?))?</div>\s*<div class="title">(.+?)</div>\s*</div>#') as $matches) {
			$this->_foundEvent($matches[0]);
			$from = new Grabber_Date($matches[1], $matches[2]);
			if (empty($matches[4])) {
				$genres = new Grabber_Genres($matches[3]);
				$description = new Grabber_Description($matches[5], null, $genres);
			} else {
				$genres = new Grabber_Genres($matches[4]);
				$description = new Grabber_Description($matches[5], $matches[3], $genres);
			}
			if ($from->getWeekday() == 6) {
				$from->setTime(23); // Sa
			} else {
				$from->setTime(20);
			}
			$this->_addEvent($description, $from);
		}
	}
}
