<?php

require_once 'List/Abstract.php';
require_once 'LocationUnknown.php';


/**
 * List_LocationsUnknowns Model
 *
 */
class List_LocationUnknowns extends List_Abstract
{
	const TYPE_ALL = self::TYPE_DEFAULT;


	/**
	 * Load events
	 *
	 */
	protected function _load() {
		switch ($this->_type) {
			case self::TYPE_ALL:
				$this->_items = $this->_getTypeAll();
				break;
			default:
				throw new Denkmal_Exception('Invalid unknwon-locations-list type (' .$this->_type. ')');
				break;
		}
	}


	/**
	 * Return all unknown locations
	 *
	 * @return array Unknown locations
	 */
	private function _getTypeAll() {
		$cacheId = 'list_locationunknowns_all';
		$sql = 'SELECT l.id
				FROM location_unknown l
				ORDER BY l.name';

		if (false === ($items = Denkmal_Cache::load($cacheId))) {
			// Cache miss
			$ids = Denkmal_Db::get()->fetchCol($sql);
			$items = array();
			foreach ($ids as $id) {
				$items[] = new LocationUnknown($id);
			}
			Denkmal_Cache::save($items, $cacheId);
		}

		return $items;
	}

}
