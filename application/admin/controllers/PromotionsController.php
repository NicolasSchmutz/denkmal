<?php

require_once 'List/Promotions.php';
require_once 'Promotion.php';
require_once 'PromotionEntry.php';


/**
 * Promotions controller
 */
class Admin_PromotionsController extends Zend_Controller_Action
{
	
	public function init()
	{
		$this->_helper->contextSwitch()
			->initContext();
			
		$this->view->headTitle('DENKMAL.ORG Admin');
	}
	
	
	/**
	 * Index page
	 */
	public function indexAction() {
		$promotions = new List_Promotions();
		
		$this->view->promotions = $promotions;
	}
	
	/**
	 * Start/stop promotion
	 */
	public function activeAction() {
		$id = intval($this->_getParam('id'));
		$active = (bool)$this->_getParam('active');
		$promotion = new Promotion($id);
		$promotion->setActive($active);
		$promotion->save();
		
		$this->_redirect('/admin/promotions/');
	}
	
	/**
	 * Entries page
	 */
	public function entriesAction() {
		$id = intval($this->_getParam('id'));
		$promotion = new Promotion($id);
		
		$this->view->promotion = $promotion;
	}
	
	/**
	 * Delete an entry
	 */
	public function delentryAction() {
		$id = intval($this->_getParam('id'));
		$promotionentry = new PromotionEntry($id);
		$promotion = $promotionentry->getPromotion();
		
		require_once 'Denkmal/Db.php';
		$db = Denkmal_Db::get();
		$db->delete('promotion_entry', 'id='.$id);

		$this->_redirect('/admin/promotions/entries/?id=' .$promotion->getId());
	}

}
