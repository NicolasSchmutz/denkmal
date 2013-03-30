<?php

require_once 'Day.php';
require_once 'List/Events.php';
require_once 'List/Locations.php';
require_once 'List/Weblinks.php';
require_once 'Event.php';
require_once 'Promotion.php';


/**
 * Index controller
 *
 * Events page
 */
class IndexController extends Zend_Controller_Action
{

	public function init()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->contextSwitch()
			->addActionContext('index', array('json', 'mobile'))
			->addActionContext('search', array('json'))
			->addActionContext('promotion', array('json'))
			->initContext();
	}


	/**
	 * Events page
	 */
	public function indexAction() {
		$day = new Day($this->_getParam(1), $this->_getParam(3));
		$events = $day->getEvents();
		$locations = new List_Locations();
		$weblinks = new List_Weblinks();
		$sm = new Event(0);

		if ($this->_getParam('ref') != 'app') {
			switch ($this->view->device = $this->_getParam('device')) {
				case 'android':
					$this->view->appLink = 'market://search?q=pname:org.denkmal.android';
					break;
			}
		}

		$this->view->sm = $sm;
		$this->view->promotion = Promotion::getActivePromotion();
		$this->view->autoplay = $this->_getParam('autoplay');
		$this->view->events = $events;
		$this->view->weblinks = $weblinks;
		$this->view->audiosData = $this->_helper->data->audios($events);
		$this->view->eventsData = $this->_helper->data->events($events);
		$this->view->locationsData = $this->_helper->data->locations($locations);
		$this->view->day = $day;
		$this->view->today = Day::now();
		$this->view->audioMode = $this->_getParam('audioMode');
		$this->view->headTitle('DENKMAL.ORG Eventkalender');
	}




	/**
	 * Search request
	 */
	public function searchAction() {
		$q = $this->_getParam('q');
		$events = new List_Events(List_Events::TYPE_SEARCH, $q);

		$this->view->q = $q;
		$this->view->audiosData = $this->_helper->data->audios($events);
		$this->view->events = $events;
	}


	/**
	 * Submit promotion request
	 */
	public function promotionAction() {
		$name = trim($this->_getParam('name'));
		$email = trim($this->_getParam('email'));
		$promotion = Promotion::getActivePromotion();

		try {
			if (strlen($name) == 0) {
				throw new Denkmal_Exception('Kein Name angegeben');
			}

			$validEmail = new Zend_Validate_EmailAddress();
			if (!$validEmail->isValid($email)) {
				throw new Denkmal_Exception('Ungültige E-Mail Adresse');
			}

			require_once 'PromotionEntry.php';
			$promotionEntry = new PromotionEntry();
			$promotionEntry->setPromotion($promotion);
			if (!$promotionEntry->setName($name)) {
				throw new Denkmal_Exception('Ungültiger Name');
			}
			if (!$promotionEntry->setEmail($email)) {
				throw new Denkmal_Exception('E-Mail Adresse ist schon eingetragen');
			}
			$promotionEntry->save();

			setcookie('promotion_'.$promotion->getId(), 1, time()+60*60*24*365, '/');

			$this->view->success = true;
			$this->view->msg = $promotion->getTextThanks();
		} catch(Denkmal_Exception $e) {
			$this->view->success = false;
			$this->view->msg = $e->getMessage();
		}
	}


	/*
	 * Google verification
	 */
	public function googleverificationAction() {
	}

}
