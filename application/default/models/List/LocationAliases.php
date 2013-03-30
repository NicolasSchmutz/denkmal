<?php

require_once 'List/Abstract.php';
require_once 'Location.php';

/**
 * List_LocationAliases Model
 *
 */
class List_LocationAliases extends List_Abstract {

	const TYPE_LOCATION = self::TYPE_DEFAULT;

	/**
	 * Load aliases
	 *
	 */
	protected function _load() {
		switch ($this->_type) {
			case self::TYPE_LOCATION:
				$this->_items = $this->_getTypeLocation($this->_filter);
				break;
			default:
				throw new Denkmal_Exception('Invalid locationalias-list type (' . $this->_type . ')');
				break;
		}
	}

	/**
	 * Return location-aliases by location
	 *
	 * @param Location $location Location
	 * @return array String-array of aliases
	 */
	private function _getTypeLocation($location) {
		$cacheId = 'list_locationaliases_location_' . $location->getId();
		$sql = 'SELECT a.name
				FROM location_alias a
				WHERE a.locationId = ?
				ORDER BY a.name';
		$args = array($location->getId());

		if (false === ($items = Denkmal_Cache::load($cacheId))) {
			// Cache miss
			$items = Denkmal_Db::get()->fetchCol($sql, $args);
			Denkmal_Cache::save($items, $cacheId);
		}

		return $items;
	}
}
