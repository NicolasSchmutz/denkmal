<?php


/**
 * String-class used by grabber
 *
 */
class Grabber_String {
	private $_string = null;

	/**
	 * Set up a string either by text or by an url to get
	 *
	 * @param mixed $string OPTIONAL Either a string OR a url to get
	 */
	function __construct($string = null) {
		if (Zend_Uri::check($string)) {
			$this->_string = $this->_getUrl($string);
		} else {
			$this->_string = $string;
		}
	}

	/**
	 * Search and replace a string
	 *
	 * @param string  $search  Search-string
	 * @param string  $replace OPTIONAL Replace-string
	 * @param boolean $regexp  OPTIONAL Use regexp-replacemant
	 * @return Grabber_String This
	 */
	public function replace($search, $replace = '', $regexp = false) {
		if ($regexp) {
			$this->_string = preg_replace($search, $replace, $this->_string);
		} else {
			$this->_string = str_replace($search, $replace, $this->_string);
		}
		return $this;
	}

	/**
	 * Strip tags from string
	 *
	 * @return Grabber_String This
	 */
	public function stripTags() {
		$this->_string = strip_tags($this->_string);
		return $this;
	}

	/**
	 * Return the piece of this string between two strings
	 *
	 * @param string  $before String before
	 * @param string  $after  String after
	 * @param boolean $greedy OPTIONAL Whether to be greedy
	 * @return Grabber_String Piece between
	 */
	public function between($before, $after, $greedy = false) {
		$regexp = '#\Q' . $before . '\E(.+';
		if (!$greedy) {
			$regexp .= '?';
		}
		$regexp .= ')\Q' . $after . '\E#i';

		if (preg_match($regexp, $this->_string, $matches)) {
			return new self($matches[1]);
		} else {
			return new self();
		}
	}

	/**
	 * Match all occurencies of a regexp and return an array of them:
	 *  array(0 => array('14', 'Juli'), 1 => array('23', 'August'))
	 *
	 * @param string $regexp The regexp to use
	 * @return array A 2-dimensional array of arrays of matches
	 */
	public function matchAll($regexp) {
		$matches = array();
		preg_match_all($regexp, $this->_string, $matches, PREG_SET_ORDER);
		return $matches;
	}

	/**
	 * Match all occurencies of a regexp and return an array of the first of every match
	 *
	 * @param string $regexp The regexp to use
	 * @return array A 1-dimensional array of first matches
	 */
	public function matchAllFirst($regexp) {
		preg_match_all($regexp, $this->_string, $matches, PREG_SET_ORDER);
		$scalars = array();
		foreach ($matches as $match) {
			if (isset($match[1])) {
				$scalars[] = $match[1];
			}
		}
		return $scalars;
	}

	/**
	 * Return the first matching parenthesis
	 *
	 * @param string  $regexp Regexp
	 * @param boolean $cut    OPTIONAL Cut in original string
	 * @return string The first match
	 */
	public function matchOne($regexp, $cut = false) {
		if (preg_match($regexp, $this->_string, $matches)) {
			if ($cut) {
				$this->cut($regexp);
			}
			if (sizeof($matches) > 1) {
				return $matches[1];
			}
		}
		return null;
	}

	/**
	 * Return all matching parenthesises
	 *
	 * @param string  $regexp Regexp
	 * @param boolean $cut    OPTIONAL Cut in original string
	 * @return array The matches
	 */
	public function match($regexp, $cut = false) {
		if (preg_match($regexp, $this->_string, $matches)) {
			if ($cut) {
				$this->cut($regexp);
			}
			if (sizeof($matches) > 1) {
				return array_slice($matches, 1);
			}
		}
		return null;
	}

	/**
	 * Cut a regexp n times out of the string
	 *
	 * @param string $regexp The regexp to cut
	 * @param int    $times  OPTIONAL How many times to cut the regexp out ("limit")
	 */
	public function cut($regexp, $times = 1) {
		if ($times === false || $times === null) {
			$times = -1;
		}
		$times = intval($times);
		$this->_string = preg_replace($regexp, '', $this->_string, $times);
	}

	public function split($regexp) {
		$parts = preg_split($regexp, $this->_string, null);
		return $parts;
	}

	/**
	 * Return URL-content
	 *
	 * @param string $url URL
	 * @return string Content
	 */
	private function _getUrl($url) {
		$context = stream_context_create(array('http' => array('ignore_errors' => true)));
		$content = file_get_contents($url, null, $context);
		$encoding = mb_detect_encoding($content, 'UTF-8, ISO-8859-1');
		$content = mb_convert_encoding($content, 'UTF-8', $encoding);
		$content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
		$content = preg_replace('/\r?\n\r?/', ' ', $content);
		$content = preg_replace('/[\xA0]/u', ' ', $content); // Replace '&nbsp' with ' '
		return $content;
	}

	public function __toString() {
		return $this->_string;
	}

}
