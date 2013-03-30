<?php

require_once 'Grabber/Calendar.php';

/**
 * Grabber for Programmzeitung
 *
 */
class Grabber_Calendar_Programmzeitung extends Grabber_Calendar {

	/**
	 * Grab events
	 *
	 * @param Zend_Date $date Date for which to get events
	 * @return Grabber
	 */
	protected function _grab($date) {
		$date_str = $date->toString('dd.MM.y');
		echo $date_str;
		$str = new Grabber_String('http://www.programmzeitung.ch/index.cfm?Datum_von=' . $date_str . '&Datum_bis=' . $date_str .
				'&Rubrik=6&uuid=2BCD9733D9D9424C4EF093B3E35CB44B');

		foreach ($str->matchAll('#<div class="veranstaltung">(.+?)</div>\s*<div class="ort">(.+?)(\[.+?\].*?)?(,.*?)?</div>\s*<div class="zeit">(\d+)\.(\d+)(\s+.\s+(\d+)\.(\d+))?</div>#u') as $matches) {
			$this->_foundEvent($matches[0]);
			$description = new Grabber_String($matches[1]);
			$description->replace('#^<b>(.+?)</b>\s*([^\s]+.+)$#u', '$1: $2', true);
			$description->stripTags();
			$locationName = new Grabber_String($matches[2]);
			$locationName->stripTags();
			$from = new Grabber_Date($date);
			$from->setTime($matches[5], $matches[6]);
			$until = null;
			if (isset($matches[8]) && isset($matches[9])) {
				$until = clone($from);
				$until->setTime($matches[8], $matches[9]);
			}
			$this->_addEvent($locationName, $description, $from, $until);
		}
	}
}
