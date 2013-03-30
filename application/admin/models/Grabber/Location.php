<?php 

require_once 'Grabber.php';


/**
 * Grabber for location-websites
 *
 */ 
abstract class Grabber_Location extends Grabber
{
	protected $_location;
	
	/**
	 * Grab events (called by grab())
	 */
	abstract protected function _grab();
	
	/**
	 * Grab events
	 * 
	 * @return Grabber
	 */
	public function grab($date = null) {
		$this->_grab();
		return $this;
	}
	
	

	/**
	 * String representation of grabber and result
	 * 
	 * @return string Grabber-result
	 */
	public function __toString() {
		return get_class($this). ' (' .$this->_countAdded. '/' .$this->_countFound. ')';
	}

	/**
	 * Add an event
	 * 
	 * @param string $description Description
	 * @param Grabber_Date $from From-date
	 * @param Grabber_Date $until OPTIONAL Until-date
	 */
	protected function _addEvent($description, $from, $until = null) {
		$from = $from->getDate();
		if ($until) $until = $until->getDate();
		$this->__addEvent($this->_location, $description, $from, $until);
	}
} 
