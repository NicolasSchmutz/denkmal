<?php 

require_once 'Grabber/Location.php';


/**
 * Grabber for Hinterhof
 *
 */ 
class Grabber_Location_HinterhofDachterrasse extends Grabber_Location
{
	/**
	 * Set up grabber
	 */
	function __construct() {
		$this->_location = Location::getLocation('Hinterhof Dachterrasse');
	}
	
	/**
	 * Grab events
	 */
	protected function _grab() {
		$str = new Grabber_String('http://www.hinterhof.ch/programm/');
		
		$str = $str->between('<ul id="events"', '</ul>');
			
		foreach ($str->matchAll('#<li class="entry dachterrasse">\s*?<div class="place">Dachterrasse</div>\s*?<a.*?>\s*?<div class="day">.*?(\d+).(\d+) - (.*?)</div>\s*?<div class="title">\s*?<span class="title">(.+?)</span>\s*?</div>\s*?<div class="teaser">(.+?)</div>\s*?</a>\s*?</li>#') as $matches) {
			$this->_foundEvent($matches[0]);
			$from = new Grabber_Date($matches[1], $matches[2]);
			$genres = new Grabber_Genres($matches[3]);
			$description = new Grabber_Description($matches[5], $matches[4], $genres);
			if ($from->getWeekday() == 6) {
				$from->setTime(23);	// Sa
			} else {
				$from->setTime(20);
			}
			$this->_addEvent($description, $from);
		}	

	}

}
