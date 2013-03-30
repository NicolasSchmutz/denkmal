<?php



/**
 * View-Helper to display a url (in list)
 *
 */
class Zend_View_Helper_AdminUrl extends Zend_Controller_Action_Helper_Abstract {

	/**
	 * Return a url
	 *
	 * @param array $url URL-item: array(id => 1, 'name' => 'Sommercasino', 'url' => 'http://www.sommercasino.ch/', 'onlyifmarked' => false)
	 * @return string HTML for the url in a list
	 */
	public function adminUrl($url) {
		$html = '';

		$html .= '<a href="del?id=' . $url['id'] . '">[del]</a> ';

		$html .= $url['name'] . ': ';

		$link = $url['url'];
		$linkPrint = preg_replace('/^http:\/\//', '', $link);
		$html .= '<a href="' . $link . '">' . $linkPrint . '</a>';

		if ($url['onlyifmarked']) {
			$html .= ' <span class="oim">(onlyifmarked)</span>';
		}

		return $html;
	}
}
