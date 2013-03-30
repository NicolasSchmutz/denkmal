<?php

require_once 'Grabber/Location.php';

/**
 * Grabber for Agora
 *
 */
class Grabber_Location_Agora extends Grabber_Location {
	/**
	 * Set up grabber
	 */
	function __construct() {
		$this->_location = Location::getLocation('Agora Bar');
	}

	/**
	 * Grab events
	 */
	protected function _grab() {
		$str = new Grabber_String('http://www.cafebaragora.org/index.php');

		$str = $str->between('<!-- text and image -->', '<!-- end text and image -->');

		foreach ($str->matchAll('#(\d+)\.(\d+)\. \| \w{2} \| \w+? \| (.+?) \| (\d+):(\d+)#') as $matches) {
			$this->_foundEvent($matches[0]);
			$from = new Grabber_Date($matches[1], $matches[2]);
			$from->setTime($matches[4], $matches[5]);

			$main = preg_replace('#\s+\|\s+#', '. ', $matches[3]);
			$description = new Grabber_Description($main);
			$this->_addEvent($description, $from);
		}

	}

}
