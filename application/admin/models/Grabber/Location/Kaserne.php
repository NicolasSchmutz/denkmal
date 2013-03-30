<?php

require_once 'Grabber/Location.php';

/**
 * Grabber for Kaserne
 *
 */
class Grabber_Location_Kaserne extends Grabber_Location {

	/**
	 * Set up grabber
	 */
	function __construct() {
		$this->_location = Location::getLocation('Kaserne');
	}

	/**
	 * Grab events
	 */
	protected function _grab() {
		$str = new Grabber_String('http://www.kaserne-basel.ch/ICAL/musik.ics');

		foreach ($str->matchAll('#BEGIN:VEVENT\s*.*?DTSTART:(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})00.*?URL:([^\s]+).*?\s*END:VEVENT#') as $matches) {
			$this->_foundEvent($matches[0]);
			$from = new Grabber_Date($matches[3], $matches[2], $matches[1]);
			$from->setTime($matches[4], $matches[5]);

			$strEvent = new Grabber_String($matches[6]);
			$genres = new Grabber_Genres(implode(',', $strEvent->matchAllFirst('#<a class="tag">(.+?)</a>#')));
			if ($matches = $strEvent->match('#<h2 class="text_Musik">(.+?)</h2>#')) {
				$description = new Grabber_Description($matches[0], null, $genres);
				$this->_addEvent($description, $from);
			}
		}
	}
}
