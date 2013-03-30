<?php

require_once 'List/Abstract.php';
require_once 'Location.php';

/**
 * List_Locations Model
 *
 */
class List_Locations extends List_Abstract {

	const TYPE_ENABLED = self::TYPE_DEFAULT; // blocked=0, enabled=1
	const TYPE_VALID = 2; // blocked=0
	const TYPE_DISABLED = 3; // enabled=0
	const TYPE_ALL = 4; //

	/**
	 * Load events
	 *
	 */
	protected function _load() {
		switch ($this->_type) {
			case self::TYPE_ENABLED:
				$this->_items = $this->_getTypeEnabled(true);
				break;
			case self::TYPE_DISABLED:
				$this->_items = $this->_getTypeEnabled(false);
				break;
			case self::TYPE_VALID:
				$this->_items = $this->_getTypeValid();
				break;
			case self::TYPE_ALL:
				$this->_items = $this->_getTypeAll();
				break;
			default:
				throw new Denkmal_Exception('Invalid locations-list type (' . $this->_type . ')');
				break;
		}
	}

	/**
	 * Return enabled locations
	 *
	 * @param boolean $enabled OPTIONAL Whether the result should be enabled (or disabled) events
	 * @return array Locations
	 */
	private function _getTypeEnabled($enabled = true) {
		$enabled = (int) (bool) $enabled;
		$cacheId = 'list_locations_enabled_' . $enabled;
		$sql = 'SELECT l.id
				FROM location l
				WHERE l.enabled=?
					AND l.blocked=0
				ORDER BY l.name';
		$args = array($enabled);

		if (false === ($items = Denkmal_Cache::load($cacheId))) {
			// Cache miss
			$ids = Denkmal_Db::get()->fetchCol($sql, $args);
			$items = array();
			foreach ($ids as $id) {
				$items[] = new Location($id);
			}
			Denkmal_Cache::save($items, $cacheId);
		}

		return $items;
	}

	/**
	 * Return all valid locations (also disabled)
	 *
	 * @return array Locations
	 */
	private function _getTypeValid() {
		$cacheId = 'list_locations_valid';
		$sql = 'SELECT l.id
				FROM location l
				WHERE l.blocked=0
				ORDER BY l.name';

		if (false === ($items = Denkmal_Cache::load($cacheId))) {
			// Cache miss
			$ids = Denkmal_Db::get()->fetchCol($sql);
			$items = array();
			foreach ($ids as $id) {
				$items[] = new Location($id);
			}
			Denkmal_Cache::save($items, $cacheId);
		}

		return $items;
	}

	/**
	 * Return all locations
	 *
	 * @return array Locations
	 */
	private function _getTypeAll() {
		$cacheId = 'list_locations_all';
		$sql = 'SELECT l.id
				FROM location l
				ORDER BY l.name';

		if (false === ($items = Denkmal_Cache::load($cacheId))) {
			// Cache miss
			$ids = Denkmal_Db::get()->fetchCol($sql);
			$items = array();
			foreach ($ids as $id) {
				$items[] = new Location($id);
			}
			Denkmal_Cache::save($items, $cacheId);
		}

		return $items;
	}
}
