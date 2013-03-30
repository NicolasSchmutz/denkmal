<?php

require_once 'Event.php';


/**
 * View-Helper to display an event (in list)
 *
 */
class Zend_View_Helper_AdminEvent extends Zend_Controller_Action_Helper_Abstract
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
	 * @return string HTML for the event in a list
	 */	
	public function adminEvent($event, $showDate = false) {
		$location = $event->getLocation();
		$from = $event->getFrom();
		$until = $event->getUntil();
		$onlySound = ($event->getId() <= 0);
		$html = '';
		
		if (!$onlySound) {
			$html .= '<div class="option star"></div>';
		}
		
		$html .= '<div class="option audio"';
		if ($event->getAudio()) {
			$html .= ' title="' .$event->getAudio(). '"';
		}
		$html .= '></div>';
		
		if (!$onlySound) {
			$html .= '<div class="option locked"></div>';
			$html .= '<div class="option del"></div>';
			$html .= '<div class="option enabled"></div>';
			$html .= '<div class="option blocked"></div>';

			if ($showDate && $from) {
				$html .= '<div class="date">' .Day::getWeekdayAbbrByIndex($from->get(Zend_Date::WEEKDAY_DIGIT)). ', ' .$from->get('d.M.'). "'" .$from->get('YY'). '</div>';
			}
		
			if ($location->getUrl()) {
				$html .= '<span class="location"><a href="' .$location->getUrl(). '">' .$location->getName(). '</a></span>';
			} else {
				$html .= '<span class="location">' .$location->getName(). '</span>';
			}
			
			$html .= ' <span class="time">';
			if ($from) {
				$html .= $from->toString('H:mm');
				if ($until) {
					$html .= '-' . $until->toString('H:mm');	
				}
			}
			$html .= '</span> - ';
		}
		
		$html .= '<span class="description">';
		$html .= $this->_view->addUrls($event->getDescription(), $event->getId());
		$html .= '</span>';
		
		return $html;
	}
}