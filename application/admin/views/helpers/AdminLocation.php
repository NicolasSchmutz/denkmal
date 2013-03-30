<?php

require_once 'Location.php';


/**
 * View-Helper to display a location (in list)
 *
 */
class Zend_View_Helper_AdminLocation extends Zend_Controller_Action_Helper_Abstract
{
	
	
	/**
	 * Return a location
	 *
	 * @param Location $location Location
	 * @return string HTML for the location in a list
	 */	
	public function adminLocation($location) {
		$html = '';
		
		if ($location->getBlocked()) {
			$html .= '<div class="info blocked">[blocked]</div>';
		} else {
			$html .= '<div class="info"><a href="/admin/locations/view/?id=' .$location->getId(). '">';
			$html .= '[' .$location->getEventsNum(). ' events]';
			$html .= '</a></div>';
		}
		
		$html .= '<span class="name">';
		if ($location->getUrl()) {
			$html .= '<a href="' .$location->getUrl(). '">' .$location->getName(). '</a>';
		} else {
			$html .= $location->getName();
		}
		$html .= '</span>';
		
		if ($notes = $location->getNotes()) {
			$html .= ' <span class="notes">(' .$notes. ')</span>';
		}
		
		$aliases = $location->getAliases();
		if ($aliases->num() > 0) {
			$html .= ' ("' .implode('", "', $aliases->get()). '")';
		}
		
		return $html;
	}
}