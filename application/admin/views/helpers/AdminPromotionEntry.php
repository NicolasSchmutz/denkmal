<?php

require_once 'PromotionEntry.php';

/**
 * View-Helper to display a promotion entry (in list)
 *
 */
class Zend_View_Helper_AdminPromotionEntry extends Zend_Controller_Action_Helper_Abstract {

	/**
	 * Return a promotion entry
	 *
	 * @param PromotionEntry $promotionentry Promotion entry
	 * @return string HTML for the promotion entry in a list
	 */
	public function adminPromotionEntry($promotionentry) {
		$html = '';

		$html .= '<div class="info">';

		$html .= '<a href="/admin/promotions/delentry/?id=' . $promotionentry->getId() . '">';
		$html .= '[del]';
		$html .= '</a> ';

		$html .= '</div>';

		$html .= '<span class="name">' . $promotionentry->getName() . '</span>';
		$html .= ' (<a class="email" href="mailto:' . $promotionentry->getEmail() . '">' . $promotionentry->getEmail() . '</a>)';

		return $html;
	}
}
