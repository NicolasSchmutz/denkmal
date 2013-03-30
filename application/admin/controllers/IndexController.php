<?php

require_once 'Day.php';
require_once 'Event.php';
require_once 'List/Events.php';
require_once 'List/Locations.php';
require_once 'List/LocationUnknowns.php';
require_once 'List/Audios.php';


/**
 * Index controller
 *
 * Admin page
 */
class Admin_IndexController extends Zend_Controller_Action
{
	
	public function init()
	{
		$this->_helper->contextSwitch()
			->addActionContext('audioall', 'json')
			->addActionContext('description', 'json')
			->initContext();
			
		$this->view->addHelperPath('../application/default/views/helpers/');
		$this->view->headTitle('DENKMAL.ORG Admin');
	}
	
	
	/**
	 * Home/Events page
	 */
	public function indexAction() {
		$day = new Day($this->_getParam('day'), $this->_getParam('week'));
		$events = $day->getEvents();
		$eventsBlocked = $day->getEventsBlocked();
		$eventsDisabled = new List_Events(List_Events::TYPE_DISABLED);
		$locationsDisabled = new List_Locations(List_Locations::TYPE_DISABLED);
		$locationUnknowns = new List_LocationUnknowns();
		$sm = new Event(0);

		$this->view->sm = $sm;
		$this->view->events = $events;
		$this->view->eventsBlocked = $eventsBlocked;
		$this->view->eventsDisabled = $eventsDisabled;
		$this->view->locationsDisabled = $locationsDisabled;
		$this->view->locationUnknowns = $locationUnknowns;
		$this->view->day = $day;
	}
	
	
	
	/**
	 * Edit event-description
	 */
	public function descriptionAction() {
	if (!$this->_hasParam('id')) {
			require_once 'Denkmal/Exception.php';
			throw new Denkmal_Exception('No event-id provided');
		}
		
		$id = intval($this->_getParam('id'));
		$description = $this->_getParam('value');
		$event = new Event($id);
		
		$event->setDescription($description, false, false);
		$event->setLocked(true);
		$event->save();
		
		$this->_helper->layout->disableLayout();
		$this->view->event = $event;
	}
	
	
	
	/**
	 * Select suggested audio
	 */
	public function audioAction() {
		if (!$this->_hasParam('id')) {
			require_once 'Denkmal/Exception.php';
			throw new Denkmal_Exception('No event-id provided');
		}
		
		$id = intval($this->_getParam('id'));
		$event = new Event($id);
		$this->view->audios = $event->getAudioSuggestions();
		$this->view->event = $event;
	}
	
	/**
	 * Send all audios, called from AJAX
	 */
	public function audioallAction() {
		$this->view->audios = new List_Audios();
	}
	
	/**
	 * Event-Option called from AJAX
	 */
	public function optionAction() {
		$id = intval($this->_getParam('id'));
		$option = $this->_getParam('option');
		$state = ($this->_getParam('state') == 'true');
		$arg = $this->_getParam('arg');
		
		$reload = false;
		$save = true;
		$event = new Event($id);
		
		switch ($option) {
			case 'star':
				$event->setStar($state);
				break;
			case 'audio':
				$event->setAudio($arg);
				break;
			case 'locked':
				$event->setLocked($state);
				break;
			case 'del':
				$event->remove();
				$save = false;
				$reload = true;
				break;
			case 'enabled':
				$event->setEnabled($state);
				$reload = true;
				break;
			case 'blocked':
				$event->setBlocked($state);
				$reload = true;
				break;
			default:
				$reload = true;
				break;
		}

		if ($save) {
			$event->save();
		}
		
		$this->_helper->json->sendJson(array('state' => $state, 'reload' => $reload));
	}
	

}
