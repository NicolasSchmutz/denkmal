<?php


/**
 * Unknown Location (name)
 *
 */
class LocationUnknown {

	private $_data = array();

	function __construct($id = null) {
		if (isset($id)) {
			$this->_load($id);
		}
	}

	/**
	 * Load a unknown location's properties
	 *
	 * @param int $id The unknown location's id
	 */
	private function _load($id) {
		$id = abs(intval($id));
		$db = Denkmal_Db::get();
		$sql = 'SELECT id, name, hits
				FROM location_unknown
				WHERE id=?';
		$this->_data = $db->fetchRow($sql, $id);

		if (!$this->_data) {
			throw new Denkmal_Exception("LocationUnknown doesn't exist (" . $id . ")");
		}
	}

	/**
	 * Return the unknown location's id
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
	 * Return the unknown location's name
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
	 * Return number of hits (event-adds) for this unknown location
	 *
	 * @return int Hits
	 */
	public function getHits() {
		if (isset($this->_data['hits'])) {
			return intval($this->_data['hits']);
		}
		return null;
	}

	/**
	 * Increase 'hit' for a (new) unknown location
	 *
	 * @param string $locationName Location-name
	 */
	public static function addHit($locationName) {
		$db = Denkmal_Db::get();
		$db->query('INSERT INTO location_unknown (name) VALUES (?)
  					ON DUPLICATE KEY UPDATE hits=hits+1', array($locationName));
		Denkmal_Cache::remove('list_locationunknowns_all');
	}
}
