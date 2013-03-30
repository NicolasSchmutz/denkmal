<?php

require_once 'Event.php';


/**
 * View-Helper to display an event-icon (eg to play sound)
 *
 */
class Zend_View_Helper_EventIcon extends Zend_Controller_Action_Helper_Abstract
{

	
	/**
	 * Return an event-icon
	 *
	 * @param Event $event Event
	 * @param boolean $tooltip OPTIONAL Whether to add a tooltip for audio-enabled icons
	 * @param string $audioMode OPTIONAL The audio-mode ('js', 'link', 'link_widget', 'none')
	 * @return string HTML for the event-icon
	 */	
	public function eventIcon($event, $tooltip = true, $audioMode = 'js') {
		if (!$audioMode) {
			$audioMode = 'js';
		}
		
		$html = '';
		
		$classes = 'eventicon';
		$dotStar = ''; $dotAudio = '';
		if ($event->getStar()) {
			$classes .= ' star';
			$dotStar = '_star';
		}
		if ($event->getAudio()) {
			$dotAudio = '_audio';
			if ($audioMode != 'none') {
				$classes .= ' audio';
			}
			if ($audioMode == 'js' || $audioMode == 'link_widget') {
				$classes .= ' audio'.md5($event->getAudio());
			}
		}
		if (!$tooltip) {
			$classes .= ' notooltip';
		}
		
		switch ($audioMode) {
			case "js":
				if ($event->getAudio()) {
					$html .= '<a href="javascript:;" class="' .$classes. '"></a>';
				} else {
					$html .= '<div class="' .$classes. '"></div>';
				}
				break;
			case 'link':
				if ($event->getAudio()) {
					$html .= '<a href="/audio/' .$event->getAudio(). '" class="' .$classes. '"></a>';
				} else {
					$html .= '<div class="' .$classes. '"></div>';
				}
				break;
			case 'link_widget':
				if ($event->getAudio()) {
					$domain = Zend_Registry::get('config')->domain;
					$html .= '<a href="http://' .$domain. '/?autoplay=' .md5($event->getAudio()). '" target="_blank" class="' .$classes. '"></a>';
				} else {
					$html .= '<div class="' .$classes. '"></div>';
				}
				break;
			case 'none':
				$html .= '<div class="' .$classes. '"></div>';
				break;
			default:
				require_once 'Denkmal/Exception.php';
				throw new Denkmal_Exception('Invalid audio-mode (' .$audioMode. ')');
				break;
		}
		
		return $html;
	}
}