<?php

require_once 'Position.php';


/**
 * Location
 *
 */
class Location
{
	private $_data = array();

	function __construct($id = null) {
		if (isset($id)) {
			$this->_load($id);
		}
	}

	/**
	 * Load a location's properties
	 *
	 * @param int $id The location's id
	 */
	private function _load($id) {
		$id = abs(intval($id));
		$db = Denkmal_Db::get();
		$sql = 'SELECT id, name, url, notes, enabled, showalways, blocked, latitude, longitude
				FROM location
				WHERE id=?';
		$this->_data = $db->fetchRow($sql, $id);

		if (!$this->_data) {
			throw new Denkmal_Exception("Location doesn't exist (" .$id.")");
		}
	}

	/**
	 * Return the location's id
	 *
	 * @return int Id
	 */
	public function getId() {
		if (isset($this->_data['id'])) {
			return intval($this->_data['id']);
		}
		return null;
	}

	/**
	 * Return the location's name
	 *
	 * @return string Name
	 */
	public function getName() {
		if (isset($this->_data['name'])) {
			return $this->_data['name'];
		}
		return null;
	}

	/**
	 * Return the location's url
	 *
	 * @return string Url
	 */
	public function getUrl() {
		if (isset($this->_data['url'])) {
			return $this->_data['url'];
		}
		return null;
	}

	/**
	 * Return the location's notes
	 *
	 * @return string Notes
	 */
	public function getNotes() {
		if (isset($this->_data['notes'])) {
			return $this->_data['notes'];
		}
		return null;
	}

	/**
	 * Return whether the location is shown always
	 *
	 * @return boolean Showalways
	 */
	public function getShowalways() {
		if (isset($this->_data['showalways']) && $this->_data['showalways']) {
			return true;
		}
		return false;
	}

	/**
	 * Return the location's geographical position
	 *
	 * @return Position Position
	 */
	public function getPosition() {
		if (isset($this->_data['latitude']) && isset($this->_data['longitude'])) {
			return new Position($this->_data['latitude'], $this->_data['longitude']);
		}
		return null;
	}

	/**
	 * Return whether this location is enabled
	 *
	 * @return boolean True if enabled
	 */
	public function getEnabled() {
		if (isset($this->_data['enabled']) && $this->_data['enabled']) {
			return true;
		}
		return false;
	}

	/**
	 * Return whether this location is blocked
	 *
	 * @return boolean True if blocked
	 */
	public function getBlocked() {
		if (isset($this->_data['blocked']) && $this->_data['blocked']) {
			return true;
		}
		return false;
	}


	/**
	 * Return events for this location
	 *
	 * @return List_Events Events list
	 */
	public function getEvents() {
		require_once 'List/Events.php';
		return new List_Events(List_Events::TYPE_LOCATION, $this);
	}

	/**
	 * Return all events for this location (also disabled and blocked)
	 *
	 * @return List_Events Events list
	 */
	public function getEventsAll() {
		require_once 'List/Events.php';
		return new List_Events(List_Events::TYPE_LOCATION_ALL, $this);
	}

	/**
	 * Return number of upcoming events
	 *
	 * @return int Number of upcoming events
	 */
	public function getEventsNum() {
		require_once 'Day.php';
		$db = Denkmal_Db::get();
		$nowStr = Day::now()->getDate()->toString('y-MM-dd');
		return $db->fetchOne('SELECT COUNT(1)
								FROM event e
								WHERE e.locationId=?
									AND e.from >= ?'
								, array($this->getId(), $nowStr));
	}

	/**
	 * Return an event at this location on a given date
	 *
	 * @param Zend_Date $date Date
	 * @return Event The event OR null
	 */
	public function getEventOn($date) {
		$morninghour = Zend_Registry::get('config')->morninghour;
		$fromStart = $date->copyPart(null);
		$fromStart->setTime('00:00:00')->addHour($morninghour);
		$fromEnd = $fromStart->copyPart(null);
		$fromEnd->addDay(1);

		$db = Denkmal_Db::get();
		$id = $db->fetchOne('SELECT id
								FROM event e
								WHERE e.locationId=?
									AND e.from > ?
									AND e.from <= ?'
								, array($this->getId(), $fromStart->toString('y-MM-dd HH:mm:ss'), $fromEnd->toString('y-MM-dd HH:mm:ss')));
		if ($id) {
			require_once 'Event.php';
			return new Event($id);
		}
		return null;
	}


	/**
	 * Return location-aliases
	 *
	 * @return array A string-array of aliases
	 */
	public function getAliases() {
		require_once 'List/LocationAliases.php';
		$aliases = new List_LocationAliases(List_LocationAliases::TYPE_LOCATION, $this);
		return $aliases;
	}


	/**
	 * Set the location's name
	 *
	 * @param string $name Name
	 * @return boolean True on success
	 */
	public function setName($name) {
		if (!$name) {
			return false;
		}
		if (self::getLocation($name)) {
			return false;
		}
		$this->_data['name'] = $name;
		return true;
	}

	/**
	 * Set the location's url
	 *
	 * @param string $url Url
	 * @return boolean True on success
	 */
	public function setUrl($url) {
		if (!Zend_Uri::check($url)) {
			return false;
		}
		$this->_data['url'] = $url;
		return true;
	}


	/**
	 * Set the location's notes
	 *
	 * @param string $notes notes
	 * @return boolean True on success
	 */
	public function setNotes($notes) {
		$this->_data['notes'] = $notes;
		return true;
	}


	/**
	 * Save/create this location
	 */
	public function save() {
		$db = Denkmal_Db::get();

		$data = $this->_data;

		if ($this->getId() === null) {
			// Create location
			$db->insert('location', $data);
			$this->_load($db->lastInsertId('location'));
		} else {
			// Update location
			$db->update('location', $data, 'id='.$this->getId());
		}

		Denkmal_Cache::clean();
	}


	/**
	 * Return a location by its name (resolves aliases)
	 *
	 * @param string $locationName The location's name
	 * @return mixed The location OR null
	 */
	public static function getLocation($locationName) {
		$db = Denkmal_Db::get();

		$id = (int) $db->fetchOne('SELECT id FROM location WHERE name=?', $locationName);
		if (!$id) {
			// Try to resolve as an alias
			$id = (int) $db->fetchOne('SELECT locationId FROM location_alias WHERE name=?', $locationName);
		}

		if ($id) {
			return new Location($id);
		}
		return null;
	}
}
