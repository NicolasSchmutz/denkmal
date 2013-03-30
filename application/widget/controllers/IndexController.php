<?php

require_once 'Day.php';
require_once 'List/Events.php';
require_once 'List/Locations.php';
require_once 'List/Weblinks.php';
require_once 'Event.php';


/**
 * Index controller
 *
 * Widget
 */
class Widget_IndexController extends Zend_Controller_Action
{
	
	public function init()
	{
		$this->view->addHelperPath('../application/default/views/helpers/');
		$this->view->setScriptPath( array_merge(array('../application/default/views/scripts'), $this->view->getScriptPaths()) );
		
		$layout = $this->_helper->layout();
		$layout->setViewSuffix('js');
		$this->getResponse()->setHeader('Content-Type', 'text/js; charset=utf-8', true);
		
		$config = Zend_Registry::get('config');
		$this->view->domain = $config->domain;
	}
	
	
	/**
	 * Widget
	 */
	public function indexAction() {
		$day = new Day();
		$events = $day->getEvents();
		
		$this->view->events = $events;
		$this->view->audiosData = $this->_helper->data->audios($events);
	}

}
