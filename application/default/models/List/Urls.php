<?php

require_once 'List/Abstract.php';

/**
 * List_Urls Model
 *
 */
class List_Urls extends List_Abstract {

	const TYPE_ALL = self::TYPE_DEFAULT;

	private $_regexpSearches = null;
	private $_regexpReplaces = null;

	/**
	 * Load urls
	 *
	 */
	protected function _load() {
		switch ($this->_type) {
			case self::TYPE_ALL:
				$this->_items = $this->_getTypeAll();
				break;
			default:
				throw new Denkmal_Exception('Invalid urls-list type (' . $this->_type . ')');
				break;
		}
	}

	/**
	 * Return all urls
	 *
	 * @return array Urls: array(3 => array('name' => 'Sommercasino', 'url' => 'http://www.sommercasino.ch/', 'onlyifmarked' => false))
	 */
	private function _getTypeAll() {
		$cacheId = 'list_urls';
		$sql = 'SELECT u.id, u.name, u.url, u.onlyifmarked
				FROM url u
				ORDER BY u.name';

		if (false === ($items = Denkmal_Cache::load($cacheId))) {
			// Cache miss
			$rows = Denkmal_Db::get()->fetchAll($sql);
			$items = array();
			foreach ($rows as $row) {
				$items[$row['id']] = array('name'         => $row['name'],
										   'url'          => $row['url'],
										   'onlyifmarked' => ($row['onlyifmarked'] == 1)
				);
			}
			Denkmal_Cache::save($items, $cacheId);
		}

		return $items;
	}

	/**
	 * Fill search- and replace-arrays for this URL-list
	 */
	private function _fillRegexps() {
		if ($this->_regexpSearches === null || $this->_regexpReplaces === null) {
			$this->_regexpSearches = array();
			$this->_regexpReplaces = array();
			$wordBoundary = '([^\w]|^|$)';

			foreach ($this->get() as $url) {
				if ($url['onlyifmarked']) {
					$this->_regexpSearches[] = '#' . $wordBoundary . '\[(\Q' . $url['name'] . '\E)\]' . $wordBoundary . '#ui';
				} else {
					$this->_regexpSearches[] = '#' . $wordBoundary . '(\Q' . $url['name'] . '\E)' . $wordBoundary . '#ui';
				}
				$this->_regexpReplaces[] = '$1<a href="' . $url['url'] . '" class="url" target="_blank">' . $url['name'] . '</a>$3';
			}
		}
	}

	/**
	 * Replace URL-names with URL-links in a string
	 *
	 * @param string $str Input-Text
	 * @return string Text with URL-links
	 */
	public function strReplace($str) {
		$this->_fillRegexps();
		$str = preg_replace($this->_regexpSearches, $this->_regexpReplaces, $str, 1);
		return $str;
	}

	/**
	 * Return matching URL-names in a string
	 *
	 * @param string $str Input-string
	 * @return array A string-array of matching names
	 */
	public function strMatches($str) {
		$this->_fillRegexps();
		$matches = array();
		foreach ($this->_regexpSearches as $search) {
			if (preg_match($search, $str, $m) > 0) {
				$matches[] = $m[2];
			}
		}
		return $matches;
	}
}
