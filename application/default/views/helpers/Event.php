<?php

require_once 'Event.php';


/**
 * View-Helper to display an event (in list)
 *
 */
class Zend_View_Helper_Event extends Zend_Controller_Action_Helper_Abstract
{
	private $_view = null;

	/**
	 * Set the current view
	 * 
	 * @param Zend_View_Interface $view Current view
	 */
	public function setView(Zend_View_Interface $view)
	{
		$this->_view = $view;
	}
	
	
	/**
	 * Return an event
	 *
	 * @param Event $event Event
	 * @param boolean $showDate OPTIONAL Whether to display the event's date
	 * @param string $audioMode OPTIONAL The audio-mode  for the EventIcon
	 * @param Location $location OPTIONAL Use this location instead the one form the event
	 * @return string HTML for the event in a list
	 */	
	public function event($event, $showDate = false, $audioMode = 'js', $location = null) {
		if (!$location) {
			$location = $event->getLocation();
		}
		$from = $event->getFrom();
		$until = $event->getUntil();
		
		$html = '';

		$html .= $this->_view->eventIcon($event, true, $audioMode);
			
		if ($showDate && $from) {
			$html .= '<span class="listdatum">' .$from->get('dd.MM'). "'" .$from->get('YY'). '</span> ';
		}
		if ($url = $location->getUrl()) {
			$html .= '<a class="location" href="' .$url. '" target="_blank" title="' .$url. '">' . $location->getName() . '</a>';
		} else {
			$html .= '<span class="location">' . $location->getName() . '</span>';
		}
		$html .= ': ';
		
		if ($from) {
			$html .= $from->toString('H:mm');
			if ($until) {
				$html .= '-' . $until->toString('H:mm');	
			}
		}
		$html .= ' ';
		
		$html .= $this->_view->addUrls( $event->getDescription(), $event->getId() );

		return $html;
	}
}