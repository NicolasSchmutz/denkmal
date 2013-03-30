<?php

require_once 'Day.php';

/**
 * View-Helper to display days-nav
 *
 */
class Zend_View_Helper_NavDays extends Zend_Controller_Action_Helper_Abstract
{	
	/**
	 * Return the days-nav
	 *
	 * @param Day $day The active day
	 * @return string HTML for days-nav
	 */	
	public function navDays($day) {
		$weekday = $day->getWeekday();
		$weekday_today = Day::now()->getWeekday();
		
		$html = '<ul id="days">';
		for ($i=0; $i<7; $i++) {
			$j = ($i+$weekday_today)%7;
			$abbr = Day::getWeekdayAbbrByIndex($j);
			$html .= '<li class="d' .$i. ' w' .$abbr;
			if ($j == $weekday) {
				$html .= ' active';
			}
			if ($i == 6) {
				$html .= ' last';
			}
			$html .= '">';
			$html .= '<a href="/' .$abbr. '" rel="address:/' .$abbr. '">' .$abbr. '</a>';
			$html .= '</li>';
		}
		
		$html .= '</ul>';
		
		return $html;
	}
}