<?php

require_once 'Grabber/Location.php';

/**
 * Grabber for Myspace-event-pages
 *
 */
abstract class Grabber_Location_Myspace extends Grabber_Location {

	/**
	 * Set up grabber
	 */
	abstract function __construct();

	/**
	 * Return the myspace-profile id
	 * @return string Profile-id
	 */
	abstract protected function _getProfileId();

	/**
	 * Grab events
	 */
	protected function _grab() {
		$str = new Grabber_String('http://events.myspace.com/' . $this->_getProfileId() . '/Events');
		$str = $str->between('<div id="home-rec-events" class="floatL marginItemsPublic">', '</div>', true);

		foreach ($str->matchAll('#<div class=\'event-titleinfo\'><a.*?><span.*?>.*?</span></a><span>.*?<span.*?>(.+?)</span></span></div><div class=\'event-cal\'>\w+?, (\w+?) (\d+?) @ (\d+?):(\d+?) (\w+)</div>#') as $matches) {
			$this->_foundEvent($matches[0]);
			$description = new Grabber_String($matches[1]);
			$from = new Grabber_Date($matches[3], $matches[2]);
			$from->setTime($matches[4], $matches[5], $matches[6]);
			$this->_addEvent($description, $from);
		}
	}
}
