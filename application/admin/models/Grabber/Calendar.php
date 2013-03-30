<?php 

require_once 'Grabber.php';


/**
 * Grabber for calendar-websites
 *
 */ 
abstract class Grabber_Calendar extends Grabber
{
	protected $_date;
	
	
	/**
	 * Grab events (called by grab())
	 * 
	 * @param Zend_Date $date Date for which to get events
	 */
	abstract protected function _grab($date);
	
	/**
	 * Grab events
	 * 
	 * @param Zend_Date $date Date for which to get events
	 * @return Grabber
	 */
	public function grab($date = null) {
		$this->_date = $date;
		$this->_resetCounters();
		$this->_grab($date);
		return $this;
	}
	

	/**
	 * String representation of grabber and result
	 * 
	 * @return string Grabber-result
	 */
	public function __toString() {
		return get_class($this). '[' .$this->_date->toString('d.M.y'). '] (' .$this->_countAdded. '/' .$this->_countFound. ')';
	}

	/**
	 * Add an event
	 * 
	 * @param string $locationName Location-name
	 * @param string $description Description
	 * @param Grabber_Date $from From-date
	 * @param Grabber_Date $until OPTIONAL Until-date
	 */
	protected function _addEvent($locationName, $description, $from, $until = null) {
		$from = $from->getDate();
		if ($until) $until = $until->getDate();
		$location = Location::getLocation($locationName);
		
		if (!$location) {
			require_once 'LocationUnknown.php';
			LocationUnknown::addHit($locationName);
		} else {		
			$this->__addEvent($location, $description, $from, $until);
		}
	}
} 
