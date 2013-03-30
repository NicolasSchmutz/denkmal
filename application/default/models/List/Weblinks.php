<?php

require_once 'List/Abstract.php';
require_once 'Weblink.php';

/**
 * List_Locations Model
 *
 */
class List_Weblinks extends List_Abstract {

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
				throw new Denkmal_Exception('Invalid weblinks-list type (' . $this->_type . ')');
				break;
		}
	}

	/**
	 * Return all weblinks
	 *
	 * @return array Weblinks
	 */
	private function _getTypeAll() {
		$cacheId = 'list_weblinks';
		$sql = 'SELECT l.url, l.description
				FROM weblink l
				ORDER BY l.url';

		if (false === ($items = Denkmal_Cache::load($cacheId))) {
			// Cache miss
			$rows = Denkmal_Db::get()->fetchAll($sql);
			$items = array();
			foreach ($rows as $row) {
				$items[] = new Weblink($row['url'], $row['description']);
			}
			Denkmal_Cache::save($items, $cacheId);
		}

		return $items;
	}
}
