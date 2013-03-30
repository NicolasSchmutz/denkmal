<?php

require_once 'List/Abstract.php';
require_once 'Denkmal/Cache.php';
require_once 'Promotion.php';


/**
 * List_Promotions Model
 * 
 */ 
class List_Promotions extends List_Abstract
{
	const TYPE_ALL = self::TYPE_DEFAULT;
	

	/**
	 * Load promotions
	 * 
	 */
	protected function _load() {
		switch ($this->_type) {
			case self::TYPE_ALL:
				$this->_items = $this->_getTypeAll();
				break;
			default:
				require_once 'Denkmal/Exception.php';
				throw new Denkmal_Exception('Invalid promotions-list type (' .$this->_type. ')');
				break;
		}
	}	
	
	/**
	 * Return all promotions
	 * 
	 * @return array Promotions
	 */
	private function _getTypeAll() {
		$sql = 'SELECT p.id
				FROM promotion p
				ORDER BY p.created DESC';

		require_once 'Denkmal/Db.php';
		$ids = Denkmal_Db::get()->fetchCol($sql);
		$items = array();
		foreach ($ids as $id) {
			$items[] = new Promotion($id);
		}
		
		return $items;	
	}

}
