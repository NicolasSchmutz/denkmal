<?php


/**
 * URLs controller
 */
class Admin_UrlsController extends Zend_Controller_Action
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
		require_once 'List/Urls.php';
		$urls = new List_Urls();
		
		$this->view->urls = $urls;
	}
	
	
	public function delAction() {
		$id = (int) $this->_getParam('id');
		
		require_once 'Denkmal/Db.php';
		$db = Denkmal_Db::get();
		$db->delete('url', 'id='.$id);
		Denkmal_Cache::clean();

		$this->_redirect('/admin/urls/');
	}
	
	
	public function createAction() {
		$name = $this->_getParam('name');
		$url = $this->_getParam('url');
		$onlyifmarked = (int)($this->_getParam('onlyifmarked') == 'on');
		
		require_once 'Denkmal/Db.php';
		$db = Denkmal_Db::get();
		$db->insert('url', array('name' => $name, 'url' => $url, 'onlyifmarked' => $onlyifmarked));
		Denkmal_Cache::clean();
		
		$this->_redirect('/admin/urls/');
	}

}
