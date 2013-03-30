<?php

require_once 'List/Urls.php';


/**
 * View-Helper to add url-links to a text
 *
 */
class Zend_View_Helper_AddUrls extends Zend_Controller_Action_Helper_Abstract
{
	private static $_urls = null;

	/**
	 * Return the input-string with added links to urls
	 *
	 * @param string $str Input string
	 * @param string $cacheId OPTIONAL Cache-id
	 * @return string String with added links
	 */
	public function addUrls($str, $cacheId = null) {
		if (isset($cacheId)) {
			$cacheId = 'str_'.$cacheId;
		}
		if (!isset($cacheId) || false === ($strResult = Denkmal_Cache::load($cacheId))) {
			$strResult = $str;

			if (!self::$_urls) {
				self::$_urls = new List_Urls();
			}

			// Add textual URL-links
			$strResult = preg_replace('#\b((http://)?(([\w_\.\-öäü]{5,}\.[a-z]{2,4})(/[/\w_\.\-öäü]*)?))\b#', '<a class="url" href="http://$3" target="_blank">$1</a>', $strResult);

			// Add URL-links from replacements
			$strResult = self::$_urls->strReplace($strResult);

			if ($cacheId) {
				Denkmal_Cache::save($strResult, $cacheId);
			}
		}
		return $strResult;
	}

}
