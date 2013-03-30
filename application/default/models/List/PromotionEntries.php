<?php

require_once 'List/Abstract.php';
require_once 'Denkmal/Cache.php';
require_once 'PromotionEntry.php';
require_once 'Promotion.php';


/**
 * List_Promotions Model
 * 
 */ 
class List_PromotionEntries extends List_Abstract
{
	const TYPE_PROMOTION = self::TYPE_DEFAULT;
	

	/**
	 * Load promotion entries
	 * 
	 */
	protected function _load() {
		switch ($this->_type) {
			case self::TYPE_PROMOTION:
				$this->_items = $this->_getTypeByPromotion($this->_filter);
				break;
			default:
				require_once 'Denkmal/Exception.php';
				throw new Denkmal_Exception('Invalid promotion entries-list type (' .$this->_type. ')');
				break;
		}
	}	
	
	/**
	 * Return promotion entries by promotion
	 * 
	 * @param Promotion $promotion Promotion to get entries from
	 * @return array Promotions
	 */
	private function _getTypeByPromotion($promotion) {
		$sql = 'SELECT e.id
				FROM promotion_entry e
				WHERE e.promotionId=?
				ORDER BY e.created DESC';
		$args = array($promotion->getId());

		require_once 'Denkmal/Db.php';
		$ids = Denkmal_Db::get()->fetchCol($sql, $args);
		$items = array();
		foreach ($ids as $id) {
			$items[] = new PromotionEntry($id);
		}
		
		return $items;	
	}

}
