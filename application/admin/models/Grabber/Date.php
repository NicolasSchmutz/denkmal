<?php

require_once 'Zend/Date.php';


/**
 * Date-class used by grabber
 *
 */ 
class Grabber_Date
{
	private static $_months = array(
		'Jan'=>1, 'Feb'=>2, 'Mar'=>3, 'Apr'=>4, 'Mai'=>5, 'Jun'=>6, 'Jul'=>7, 'Aug'=>8, 'Sep'=>9, 'Okt'=>10, 'Nov'=>11, 'Dez'=>12,
		'Januar'=>1, 'Februar'=>2, 'März'=>3, 'April'=>4, 'Mai'=>5, 'Juni'=>6, 'Juli'=>7, 'August'=>8, 'September'=>9, 'Oktober'=>10, 'November'=>11, 'Dezember'=>12,
		'Sept'=>9,
		);
	private static $_now = null;
	
	/**
	 * @var Zend_Date
	 */
	private $_date = null;
	
	
	/**
	 * Set up an upcoming date
	 * 
	 * @param mixed $day Day of month (1..31) OR a Zend_Date
	 * @param mixed $month OPTIONAL Month (1..12 / "Jan", "Feb", ...)
	 * @param int $year OPTIONAL Year
	 * @throws Denkmal_Exception on invalid/strange values
	 */
	function __construct($day, $month = null, $year = null) {
		$config = Zend_Registry::get('config');
		
		if ($day instanceof Zend_Date) {
			$this->_date = $day->copyPart(null);
		} else {
			$this->_date = new Zend_Date();
			
			if ($day >= 1 && $day <= 31) {
				$day = (int)$day;
			} else {
				require_once 'Denkmal/Exception.php';
				throw new Denkmal_Exception('Unknown day (' .$day. ')');
			}
	
			if ($month >= 1 && $month <= 12) {
				$month = (int)$month;
			} elseif (array_key_exists($month, self::$_months)) {
				$month = self::$_months[$month];
			} else {
				require_once 'Denkmal/Exception.php';
				throw new Denkmal_Exception('Unknown month (' .$month. ')');
			}
			
			$yearNow = $this->_getNow()->get('y');
			$yearGuess = false;
			if (isset($year)) {
				if (strlen($year) == 2) {
					$year = substr($yearNow, 0, 2) . $year;
				}
				if ($year >= $yearNow-1 && $year <= $yearNow+2) {
					$year = (int)$year;
				} else {
					require_once 'Denkmal/Exception.php';
					throw new Denkmal_Exception('Unknown year (' .$year. ')');
				}
			} else {
				$year = $yearNow;
				$yearGuess = true;
			}
			
			$this->_date = new Zend_Date(array('year' => $year, 'month' => $month, 'day' => $day, 'hour' => 0));
			
			if ($yearGuess) {
				$minDate = clone($this->_getNow());
				$minDate->subMonth($config->grabber->tresholdNextyear);
				if ($this->_date->compare($minDate) < 0) {
					// Date is more than [tresholdNextyear] months in past -> set to next year
					$this->_date->addYear(1);
				}
			}
		}
		
		$this->_date->setTime($config->grabber->defaultTime);
	}
	
	public function setTime($hours, $minutes = null, $ampm = null) {
		if (is_array($hours) && count($hours) >= 2) {
			$hours = $hours[0];
			$minutes = $hours[1];
		}
		if (strtolower($ampm) == 'pm' && $hours <= 12) {
			$hours += 12;
		}
		if ($hours == 24 && $minutes == 0) {
			$hours = 0;
			$this->_date->addDay(1);
		} 
		if (isset($hours) && $hours >= 0 && $hours <= 24) {
			$this->_date->setHour((int)$hours);
		}
		if (isset($minutes) && $minutes >= 0 && $minutes <= 60) {
			$this->_date->setMinute((int)$minutes);
		}
	}
	
	
	/**
	 * Return date
	 * 
	 * @return Zend_Date Date
	 */
	public function getDate() {
		return $this->_date;
	}
	
	/**
	 * Return weekday
	 * 
	 * @return int Weekday (0=So, 1=Mo, 2=Di, ..., 6=Sa)
	 */
	public function getWeekday() {
		return $this->_date->get(Zend_Date::WEEKDAY_DIGIT);
	}
	
	public function __clone() {
		$this->_date = clone($this->_date);
	}
	
	/**
	 * Get current date
	 * 
	 * @return Zend_Date Now
	 */
	private function _getNow() {
		if (!self::$_now) {
			self::$_now = new Zend_Date();
		}
		return self::$_now;
	}
	
	public function __toString() {
		return $this->_date->toString();
	} 
	
} 
