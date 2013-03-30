<?php 

require_once 'Grabber/Location.php';


/**
 * Grabber for Chez Soif
 *
 */ 
class Grabber_Location_Chezsoif extends Grabber_Location
{
	/**
	 * Set up grabber
	 */
	function __construct() {
		$this->_location = Location::getLocation('Chez Soif');
	}
	
	/**
	 * Grab events
	 */
	protected function _grab() {
		$str = new Grabber_String('http://www.chezsoif.ch/cs/Veranstaltungen.html');

		$str->replace('#<br\s*/>#', ' ', true);
		$str->replace('#<span[^<]*>(.*?)</span>#', '$1', true);
		$str->replace('#<p[^<]*>(.*?)</p>#', '$1', true);

		foreach ($str->matchAll('#(\d{1,2})[\. ]+((\d{1,2}|\w{3,9}))[\. ]+(\d{2,4})\s+(\b[^<]+?)\s{5,}#') as $matches) {
			$this->_foundEvent($matches[0]);
			$description = new Grabber_String($matches[5]);
			$from = new Grabber_Date($matches[1], $matches[2], $matches[4]);
			$from->setTime(21);
			$from->setTime( $description->match('#um\s+(\d+)\.(\d+)\s+Uhr#i', true) );
			$this->_addEvent($description, $from);
		}
	}

} 
