<?php

require_once 'Promotion.php';


/**
 * View-Helper to display a promotion (in list)
 *
 */
class Zend_View_Helper_AdminPromotion extends Zend_Controller_Action_Helper_Abstract
{
	
	
	/**
	 * Return a promotion
	 *
	 * @param Promotion $promotion Promotion
	 * @return string HTML for the promotion in a list
	 */	
	public function adminPromotion($promotion) {
		$html = '';
		
		$html .= '<div class="info">';
		
		if ($promotion->getActive()) {
			$html .= '<a href="/admin/promotions/active/?id=' .$promotion->getId(). '&active=0">';
			$html .= '[stop]';
			$html .= '</a> ';
		} else {
			$html .= '<a href="/admin/promotions/active/?id=' .$promotion->getId(). '&active=1">';
			$html .= '[start]';
			$html .= '</a> ';
		}
		
		if ($entriesNum = $promotion->getEntriesNum()) {
			$html .= '<a href="/admin/promotions/entries/?id=' .$promotion->getId(). '">';
			$html .= '[' .$entriesNum. ' entries]';
			$html .= '</a>';
		} else {
			$html .= '[0 entries]';
		}
		
		$html .= '</div>';
		
		$html .= '<span class="name">' .strip_tags($promotion->getText()). '</span>';

		return $html;
	}
}