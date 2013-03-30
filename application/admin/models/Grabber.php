<?php 

require_once 'Event.php';
require_once 'Location.php';
require_once 'Day.php';
require_once 'Grabber/String.php';
require_once 'Grabber/Date.php';
require_once 'Grabber/Genres.php';
require_once 'Grabber/Description.php';


/**
 * Grabber
 *
 */ 
abstract class Grabber
{
	
	protected $_countFound = 0;
	protected $_countAdded = 0;
	
	/**
	 * String representation of grabber and result
	 * 
	 * @return string Grabber-result
	 */
	abstract public function __toString();
	
	/**
	 * Grab events
	 * 
	 * @param Zend_Date $date OPTIONAL Date for which to get events (only for Grabber_Calendar)
	 * @return Grabber
	 */
	abstract public function grab($date = null);
	
	/**
	 * Reset counters for found- and added-count
	 */
	protected function _resetCounters() {
		$this->_countAdded = 0;
		$this->_countFound = 0;
	}
	
	/**
	 * Call when an event was found
	 * 
	 * @param string $str Debug-information about the found event
	 */
	protected function _foundEvent($str = null) {
		$this->_countFound++;
		if ($str && Zend_Registry::get('config')->grabber->debug) {
			echo '<pre>' .htmlspecialchars($str). '</pre>';
		}
	}
	
	/**
	 * Formatting-fixes for descriptions
	 * 
	 * @param string $description Description
	 * @return string Fixed description
	 */
	protected function _formatDescription($description) {
		$description = preg_replace('#\[(.+?)\]#', '($1)', $description);
		$description = preg_replace('#\bdj[\'`]s\b#', 'DJs', $description);
		return $description;
	}
	
	
	/**
	 * Add an event from grabber
	 * 
	 * @param Location $location Location
	 * @param string $description Description
	 * @param Zend_Date $from From-date
	 * @param Zend_Date $until OPTIONAL Until-date
	 */
	protected function __addEvent($location, $description, $from, $until = null) {
		$config = Zend_Registry::get('config');
		
		// Compile event
		$event = new Event();
		$description = $this->_formatDescription($description);
		if (!$event->setDescription($description)) return false;
		if (!$event->setFrom($from)) return false;
		if (!$event->setLocation($location)) return false;
		if ($until) {
			if ($until->isEarlier($from)) {
				$until->addDay(1);
			}
			if (!$event->setUntil($until)) return false;
		}
		
		// Decide what to do with event
		if ($location->getBlocked()) {
			// Location blocked -> Ignore
			return false;
		}
		$maxFrom = Day::now()->getDate()->addDay($config->grabber->maxDays);
		if ($event->getFrom()->compareDate($maxFrom) > 0) {
			// Event too far in the future
			return false;
		}
		$eventExisting = $location->getEventOn( $from->subHour($config->morninghour) );
		if ($eventExisting && ($eventExisting->getLocked() || $eventExisting->getBlocked())) {
			// Event at location is locked or blocked -> Ignore
			return false;
		}
		
		$maxDisable = Day::now()->getDate()->addDay($config->grabber->disableWithin);
		if ($eventExisting) {
			// Event is not locked/blocked
			if ($event->getDescription() != $eventExisting->getDescription()
				|| $event->getFrom() != $eventExisting->getFrom()
				|| $event->getUntil() != $eventExisting->getUntil()) {
					// Event has changes -> update
					$eventExisting->setDescription( $event->getDescription() );
					$eventExisting->setFrom( $event->getFrom() );
					$eventExisting->setUntil( $event->getUntil() );
					if ($eventExisting->getFrom()->compareDate($maxDisable) <= 0) {
						/* 
						 * Don't disable changed unlocked events.
						 * Events which are listed in multiple grabbers, would be disabled every grabbing,
						 * if the two listings differ.
						 */ 
						//$eventExisting->setEnabled(false);
					}
					$eventExisting->save();
			}
		} else {
			// New event
			if ($event->getFrom()->compareDate($maxDisable) > 0) {
				$event->setEnabled(true);
			}
			$event->save();
		}
		
		$this->_countAdded++;
		
		if (Zend_Registry::get('config')->grabber->debug) {
			echo $event->getFrom();
			if ($event->getUntil()) { echo ' - ' . $event->getUntil(); }
			echo ': ' .print_r($event, true);
			echo "<br/><br/>";
		}
		
		return true;
	}
} 
