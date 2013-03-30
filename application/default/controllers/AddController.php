<?php

require_once 'Day.php';
require_once 'Zend/Date.php';
require_once 'List/Events.php';
require_once 'List/Locations.php';
require_once 'Event.php';
require_once 'Location.php';


/**
 * Add controller
 */
class AddController extends Zend_Controller_Action
{
	
	public function init()
	{
		$this->view->headLink()->appendStylesheet('/css/add.css');
		$this->view->headScript()->appendFile('/js/add.js');
		
		$this->_helper->contextSwitch()
			->addActionContext('preview', 'json')
			->addActionContext('events', 'json')
			->initContext();
	}
	
	
	/**
	 * Add page
	 */
	public function indexAction() {
		$event = $location = null;
		$errors = $this->_parseForm($event, $location);

		if ($this->_request->isPost() && sizeof($errors) == 0) {				
			// Set pseudo values to store additional information
			if (!empty($this->view->description['links'])) {
				$event->setDescription($event->getDescription() . ' / ' . $this->view->description['links'], false);
			}
			
			if (!$location->getId()) {
				$location->save();
				$this->view->locationId = $location->getId();
			}		
			$event->setLocked(true);	
			$event->setLocation($location);
			$event->save();
			
			$this->renderScript('add/thanks.phtml');
		}
		
		$this->view->errors = $errors;
		$this->view->locations = new List_Locations(List_Locations::TYPE_VALID);
		$this->view->headTitle('DENKMAL.ORG Event hinzufügen');
	}
	
	
	/**
	 * Event-preview
	 */
	public function previewAction() {
		$this->_parseForm($event, $location);
		
		if ($from = $event->getFrom()) {
			$morninghour = Zend_Registry::get('config')->morninghour;
			
			if ($from->compareTime($morninghour.':00:00') < 0) {
				$fromDay = new Day(null, null, $from);
				$fromDay->getDate()->subDay(1);
				$this->view->notice = 'Dieser Event wird am <em>' .$this->view->day($fromDay). '</em> angezeigt (wegen frühmorgendlichem Anfang)!';
			}
		}
		
		$this->view->event = $event;
		$this->view->location = $location;
	}
	
	/**
	 * Events-list for location
	 */
	public function eventsAction() {
		$locationId = intval( $this->_getParam('location_id') );
		$location = new Location($locationId);
		$events = $location->getEvents();
		
		$this->view->events = $events;
	}
	
	
	
	/**
	 * Parse the event-adding-form
	 * 
	 * @param Event $event The resulting event
	 * @param Location $location The resulting location
	 * @return array An array of error-msgs
	 */
	private function _parseForm(&$event, &$location) {
		$errors = array();
		$now = Day::now()->getDate();
		$event = new Event();
		
		$this->_request->setParamSources(array('_POST', '_GET'));
		
		$locationId = intval( $this->getParam('location_id') );
		$locationName = $this->getParam('location_name'); 
		$locationPlace = $this->getParam('location_place'); 
		$locationUrl = $this->getParam('location_url');
			if (!$this->_hasParam('location_url')) $locationUrl = 'http://';
		$fromDate = $this->getParam('from_date', $now->get('d.M.y'));
		$fromTime = $this->getParam('from_time', '22:00');
		$untilTime = $this->getParam('until_time');
		$description = $this->getParam('description');
		
		if (!Zend_Date::isDate($fromDate, 'd.M.y')) {
			$errors[] = 'Ungültiges Datum';
		} else if (!Zend_Date::isDate($fromTime, 'H:mm')) {
			$errors[] = 'Ungültige Zeit';
		} else {
			$from = new Zend_Date($fromDate.' '.$fromTime, 'd.M.y H:mm');
			if (!$event->setFrom($from)) {
				$errors[] = 'Ungültiger Zeitpunkt';
			}
		}
		
		if ($untilTime) {
			if (!Zend_Date::isDate($untilTime, 'H:mm')) {
				$errors[] = 'Ungültige End-Zeit';
			} else if (isset($from)) {
				$until = clone($from);
				$until->setTime(0);
				@$until->addTime($untilTime.':00');
				if ($until->isEarlier($from)) {
					$until->addDay(1);
				}
				$event->setUntil($until);
			}
		}
		
		if (!$locationId) {
			if ($location = Location::getLocation($locationName)) {
				// Location with this name already exists
				$locationId = $location->getId();
			} else {
				$location = new Location();
				if (!$location->setName($locationName) && $this->_request->isPost()) {
					$errors[] = 'Ungültiger Location-Name';
				}
				if ($locationPlace && !$location->setNotes($locationPlace) && $this->_request->isPost()) {
					$errors[] = 'Ungültiger Location-Ort';
				}
				if ($locationUrl && !$location->setUrl($locationUrl) && $this->_request->isPost()) {
					$errors[] = 'Ungültige Location-Webseite';
				}
			}
		} else {
			try {
				$location = new Location($locationId);
			} catch(Denkmal_Exception $e) {
				$errors[] = 'Ungültige location';
			}
		}
		
		$descriptionText = '';
		if (!empty($description['title'])) {
			$descriptionText .= $description['title'];
		}
		if (!empty($description['artists'])) {
			if (!empty($descriptionText)) {
				$descriptionText .= ': ';
			}
			$descriptionText .= $description['artists'];
		}
		if (!empty($description['genres'])) {
			if (!empty($descriptionText)) {
				$descriptionText .= '. ';
			}
			$descriptionText .= ucfirst(strtolower($description['genres']));
		}
		if (!$event->setDescription($descriptionText) && $this->_request->isPost()) {
			$errors[] = 'Die Beschreibung des Events ist unvollständig';
		}
		
		$this->view->now = $now->getDate();
		$this->view->locationId = $locationId;
		$this->view->locationName = $locationName;
		$this->view->locationPlace = $locationPlace;
		$this->view->locationUrl = $locationUrl;
		$this->view->fromDate = $fromDate;
		$this->view->fromTime = $fromTime;
		$this->view->untilTime = $untilTime;
		$this->view->description = $description;
		
		return $errors;
	}
	
	
	/**
	 * Return a request-param in sequence: POST, GET
	 * @param string $key Param-key
	 * @param string $default OPTIONA Default-value
	 * @return mixes The param-value OR null
	 */
	private function getParam($key, $default = null) {
		if ($value = $this->_request->getPost($key)) {
			return $value;
		} else if ($value = $this->_request->getQuery($key)) {
			return $value;
		}
		return $default;
	}
	
}
