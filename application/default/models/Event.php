<?php

require_once 'Day.php';
require_once 'Location.php';


/**
 * Event
 *
 */
class Event
{
	private $_data = array();

	function __construct($id = null) {
		if (isset($id)) {
			$this->_load($id);
		}
	}

	/**
	 * Load an event's properties
	 *
	 * @param int $id The event's id
	 */
	private function _load($id) {
		$id = intval($id);
		$db = Denkmal_Db::get();
		$sql = 'SELECT id, locationId, UNIX_TIMESTAMP(`from`) AS `from` , UNIX_TIMESTAMP(until) AS until,
					description, enabled, star, audio, locked, blocked
				FROM event
				WHERE id=?';
		$this->_data = $db->fetchRow($sql, $id);

		if (!$this->_data) {
			throw new Denkmal_Exception("Event doesn't exist (" .$id.")");
		}
	}

	/**
	 * Return the event's id
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
	 * Return the event's location
	 *
	 * @return Location Location
	 */
	public function getLocation() {
		if (isset($this->_data['locationId'])) {
			return new Location(intval($this->_data['locationId']));
		}
		return null;
	}

	/**
	 * Return the event's from-date
	 *
	 * @return Zend_Date From
	 */
	public function getFrom() {
		if (isset($this->_data['from'])) {
			return new Zend_Date(intval($this->_data['from']), Zend_Date::TIMESTAMP);
		}
		return null;
	}

	/**
	 * Return the event's until-date
	 *
	 * @return Zend_Date Until
	 */
	public function getUntil() {
		if (isset($this->_data['until'])) {
			return new Zend_Date(intval($this->_data['until']), Zend_Date::TIMESTAMP);
		}
		return null;
	}

	/**
	 * Return the event's description
	 *
	 * @return string Description
	 */
	public function getDescription() {
		if (isset($this->_data['description'])) {
			return $this->_data['description'];
		}
		return null;
	}


	/**
	 * Return whether the event has a star
	 *
	 * @return boolean Star
	 */
	public function getStar() {
		if (isset($this->_data['star']) && $this->_data['star']) {
			return true;
		}
		return false;
	}

	/**
	 * Return whether the event is enabled
	 *
	 * @return boolean Enabled
	 */
	public function getEnabled() {
		if (isset($this->_data['enabled']) && $this->_data['enabled']) {
			return true;
		}
		return false;
	}

	/**
	 * Return whether the event is blocked
	 *
	 * @return boolean Blocked
	 */
	public function getBlocked() {
		if (isset($this->_data['blocked']) && $this->_data['blocked']) {
			return true;
		}
		return false;
	}

	/**
	 * Return whether the event is locked
	 *
	 * @return boolean Locked
	 */
	public function getLocked() {
		if (isset($this->_data['locked']) && $this->_data['locked']) {
			return true;
		}
		return false;
	}

	/**
	 * Return the event's audio file
	 *
	 * @return string Audio-file
	 */
	public function getAudio() {
		if (isset($this->_data['audio'])) {
			return $this->_data['audio'];
		}
		return null;
	}


	/**
	 * Return audio-suggestions for this event
	 *
	 * @return List_Audios Suggested audios
	 */
	public function getAudioSuggestions() {
		require_once 'List/Audios.php';
		return new List_Audios(List_Audios::TYPE_SUGGESTIONS, $this);
	}


	/**
	 * Set description
	 *
	 * @param string $description Description
	 * @param boolean $lenghtLimit OPTIONAL Whether to shorten long descriptions (default: true)
	 * @param boolean $filterFormat OPTIONAL Whether to filter formatting in description (default: true)
	 * @return boolean True on success
	 */
	public function setDescription($description, $lenghtLimit = true, $filterFormat = true) {
		// Formatierung
		if ($filterFormat) {
			$description = preg_replace('/\r?\n\r?/', ' ', $description);
			$description = strip_tags($description);
			$description = preg_replace('/\b([A-ZÖÄÜ])([A-ZÖÄÜ]{2,})\b/e', "'\\1'.strtolower('\\2')", $description);
			$description = preg_replace('/\s+/u', ' ', $description);
			$description = trim($description);
			$description = preg_replace('/\bDJ[\'`‛’‘]?(s?)\b/i', 'DJ$1', $description);
		}
		// Länge
		if ($lenghtLimit) {
			$description = substr($description, 0, 500);
		}

		if (!$description) {
			return false;
		}

		$this->_data['description'] = $description;
		return true;
	}


	/**
	 * Set the from date/time
	 *
	 * @param Zend_Date $from Event-start
	 * @return boolean True on success
	 */
	public function setFrom($from) {
		if ($from->isEarlier(Day::now()->getDate())) {
			return false;
		}
		$this->_data['from'] = $from->get(Zend_Date::TIMESTAMP);
		return true;
	}

	/**
	 * Set the until date/time
	 *
	 * @param Zend_Date $until Event-end
	 * @return boolean True on success
	 */
	public function setUntil($until) {
		if (!$until) {
			$this->_date['until'] = null;
			return true;
		}
		if (($from = $this->getFrom()) && $until->isEarlier($from)) {
			return false;
		}
		$this->_data['until'] = $until->get(Zend_Date::TIMESTAMP);
		return true;
	}

	/**
	 * The the event-location
	 *
	 * @param Location $location The location
	 * @return boolean True on success
	 */
	public function setLocation($location) {
		if ($this->_data['locationId'] = $location->getId()) {
			return true;
		}
		return false;
	}

	/**
	 * Set star
	 *
	 * @param boolean $star Star
	 */
	public function setStar($star) {
		$this->_data['star'] = (int) (boolean) $star;
	}

	/**
	 * Set blocked
	 *
	 * @param boolean $blocked Blocked
	 */
	public function setBlocked($blocked) {
		$this->_data['blocked'] = (int) (boolean) $blocked;
	}

	/**
	 * Set locked
	 *
	 * @param boolean $locked Locked
	 */
	public function setLocked($locked) {
		$this->_data['locked'] = (int) (boolean) $locked;
	}

	/**
	 * Set enabled
	 *
	 * @param boolean $enabled Enabled
	 */
	public function setEnabled($enabled) {
		$this->_data['enabled'] = (int) (boolean) $enabled;
	}


	/**
	 * Set audio
	 *
	 * @param string $audio OPTIONAL Audio-file (no argument -> remove audio)
	 */
	public function setAudio($audio = null) {
		$this->_data['audio'] = $audio;
	}






	/**
	 * Remove this event
	 */
	public function remove() {
		if ($this->getId()) {
			$db = Denkmal_Db::get();
			$db->delete('event', 'id='.$this->getId());
		}
		$this->_data = array();
		Denkmal_Cache::clean();
	}


	/**
	 * Save/create this event
	 */
	public function save() {
		$db = Denkmal_Db::get();

		$data = $this->_data;
		if ($from = $this->getFrom()) {
			$data['from'] = $from->toString('y-MM-dd HH:mm:ss');
		}
		if ($until = $this->getUntil()) {
			$data['until'] = $until->toString('y-MM-dd HH:mm:ss');
		}

		if ($this->getId() === null) {
			// Create event
			$db->insert('event', $data);
			$this->_load($db->lastInsertId('event'));
		} else {
			// Update event
			$db->update('event', $data, 'id='.$this->getId());
		}

		Denkmal_Cache::clean();
	}
}
