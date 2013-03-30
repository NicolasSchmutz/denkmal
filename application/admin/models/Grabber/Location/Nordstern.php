<?php

require_once 'Grabber/Location.php';

/**
 * Grabber for Nordstern
 *
 */
class Grabber_Location_Nordstern extends Grabber_Location {
	/**
	 * Set up grabber
	 */
	function __construct() {
		$this->_location = Location::getLocation('Nordstern');
	}

	/**
	 * Grab events
	 */
	protected function _grab() {
		$str = new Grabber_String('http://nordstern.com/nordstern/de/programm.html');

		foreach ($str->matchAll('#<a href="(/nordstern/de/programm/.*?)">#') as $matches) {
			$this->_foundEvent($matches[0]);
			$strEvent = new Grabber_String('http://nordstern.com' . $matches[1]);
			$strEvent = $strEvent->between('<div class="event">', '<div class="text">');

			if (!($date = $strEvent->match('#<span class="date">(\d+)-(\d+)-(\d+)</span>#'))) {
				continue;
			}
			$from = new Grabber_Date($date[0], $date[1], $date[2]);
			$from->setTime(22);
			$from->setTime($strEvent->matchOne('#Türöffnung (\d+)#'));

			$title = $strEvent->matchOne('#<p class="title">(.+?)</p>#');
			$artists = $strEvent->matchAllFirst('#<p class="artist">\s*(.+?)\s*(?:<span class="description">.+?</span>)?\s*(?:<a.+?>.*?</a>)?\s*</p>#');
			foreach ($artists as &$artist) {
				$artist = trim(strip_tags($artist));
			}
			$artists = array_filter($artists);
			$description = new Grabber_Description(implode(', ', $artists), $title);
			$this->_addEvent($description, $from);
		}

	}

}
