<?php

require_once 'Day.php';

/**
 * View-Helper to display days-nav
 *
 */
class Zend_View_Helper_AdminNavDays extends Zend_Controller_Action_Helper_Abstract {

	/**
	 * Return the admin days-nav
	 *
	 * @param Day $day The active day
	 * @return string HTML for days-nav
	 */
	public function adminNavDays($day) {
		$weekday = $day->getWeekday();
		$dayNow = Day::now();
		$weekdayNow = $dayNow->getWeekday();
		$weekOffset = floor(-($dayNow->getDate()->sub($day->getDate())) / (60 * 60 * 24 * 7));

		$html = '<ul id="days" class="nav">';
		for ($i = 0; $i < 10; $i++) {
			$j = ($i + $weekdayNow) % 7;
			$abbr = Day::getWeekdayAbbrByIndex($j);
			$week = floor($i / 7);
			$html .= '<li class="';
			if ($j == $weekday && $week == $weekOffset) {
				$html .= ' active';
			}
			$html .= '">';
			$html .= '<a href="?day=' . $abbr . '&week=' . $week . '">' . $abbr;
			if ($week > 0) {
				$html .= '+' . $week;
			}
			$html .= '</a>';
			$html .= '</li>';
		}

		$html .= '</ul>';

		return $html;
	}
}
