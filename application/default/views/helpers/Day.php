<?php

require_once 'Day.php';

/**
 * View-Helper to display a day as string
 *
 */
class Zend_View_Helper_Day extends Zend_Controller_Action_Helper_Abstract {

	/**
	 * Return a day as string
	 *
	 * @param Day     $day     The day
	 * @param boolean $weekday Show weekday
	 * @return string The day
	 */
	public function day($day, $weekday = true) {
		$str = '';
		if ($weekday) {
			$str .= $day->getWeekdayAbbr() . ', ';
		}
		$str .= $day->getDate()->toString('d.M.y');
		return $str;
	}
}
