<?php

require_once 'List/Events.php';

/**
 * Day
 *
 */
class Day {

	private static $_weekdays = array('so', 'mo', 'di', 'mi', 'do', 'fr', 'sa');
	private $_date;

	/**
	 * Construct an eventcalendar-day
	 *
	 * @param string    $weekdayStr OPTIONAL The weekday ('Mo', 'Di', ...)
	 * @param int       $weekOffset OPTIONAL Add offset in weeks
	 * @param Zend_Date $date       OPTIONAL Set the base-date by a Zend_Date
	 */
	function __construct($weekdayStr = null, $weekOffset = null, $date = null) {
		if (!$date) {
			$date = $this->_now();
		}

		$date->setTime('12:00:00');

		if ($weekdayStr) {
			$weekday = array_search(strtolower($weekdayStr), self::$_weekdays);
			$weekday_now = $date->get(Zend_Date::WEEKDAY_DIGIT);
			$dayOffset = $weekday - $weekday_now;
			if ($dayOffset < 0) {
				$dayOffset += 7;
			}
			$date->addDay($dayOffset);
		}

		if ($weekOffset) {
			$date->addWeek($weekOffset);
		}

		$this->setDate($date);
	}

	/**
	 * Return the day as a date
	 *
	 * @return Zend_Date The date
	 */
	public function getDate() {
		return $this->_date;
	}

	/**
	 * Set the date
	 *
	 * @param Zend_Date $date The date
	 */
	public function setDate($date) {
		$this->_date = new Zend_Date(array('year'  => $date->get(Zend_Date::YEAR),
										   'month' => $date->get(Zend_Date::MONTH),
										   'day'   => $date->get(Zend_Date::DAY),
										   'hour'  => 0));
	}

	/**
	 * Return all events for this day
	 *
	 * @return List_Events Events list
	 */
	public function getEvents() {
		return new List_Events(List_Events::TYPE_DAY, $this);
	}

	/**
	 * Return all blocked events for this day
	 *
	 * @return List_Events Blocked events list
	 */
	public function getEventsBlocked() {
		return new List_Events(List_Events::TYPE_DAY_BLOCKED, $this);
	}

	/**
	 * Return the day's weekday
	 *
	 * @return int Weekday-index
	 */
	public function getWeekday() {
		return $this->_date->get(Zend_Date::WEEKDAY_DIGIT);
	}

	/**
	 * Get the day's weekday-abbrevation
	 *
	 * @return string The weekday's 2-letter abbrevation
	 */
	public function getWeekdayAbbr() {
		return self::getWeekdayAbbrByIndex($this->getWeekday());
	}

	/**
	 * Get a weekday-abbrevation by weekday-index
	 *
	 * @return string The weekday's 2-letter abbrevation
	 */
	public static function getWeekdayAbbrByIndex($weekday) {
		if (isset(self::$_weekdays[$weekday])) {
			return ucfirst(self::$_weekdays[$weekday]);
		}
		return null;
	}

	/**
	 * Return current day (with "morninghour"-offset)
	 *
	 * @return Day Current day
	 */
	public static function now() {
		return new Day();
	}

	/**
	 * Return current date (with "morninghour"-offset)
	 *
	 * @return Zend_Date Current date
	 */
	private function _now() {
		//return new Zend_Date('2010-03-27T12:00:00', Zend_Date::ISO_8601);
		$morninghour = Zend_Registry::get('config')->morninghour;
		return Zend_Date::now()->subHour($morninghour);
	}

	/**
	 * String representation of the date
	 *
	 * @return string String representation
	 */
	public function __toString() {
		return $this->_date->toString();
	}
}
