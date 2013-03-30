<?php

require_once 'List/Locations.php';
require_once 'Location.php';


/**
 * Locations controller
 */
class Admin_LocationsController extends Zend_Controller_Action
{
	
	public function init()
	{
		$this->_helper->contextSwitch()
			->initContext();
			
		$this->view->addHelperPath('../application/default/views/helpers/');
		$this->view->headTitle('DENKMAL.ORG Admin');
	}
	
	
	/**
	 * Index page
	 */
	public function indexAction() {
		$locations = new List_Locations(List_Locations::TYPE_ALL);
		
		$this->view->locations = $locations;
	}
	
	
	/**
	 * List events for a location
	 */
	public function viewAction() {
		$id = intval($this->_getParam('id'));
		$location = new Location($id);
		$events = $location->getEventsAll();
		
		$this->view->location = $location;
		$this->view->events = $events;
	}

}
