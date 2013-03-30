<?php

require_once 'Grabber/String.php';

/**
 * Description used by grabber
 *
 */
class Grabber_Description {

	private $_title = null;
	private $_main = null;
	private $_genres = null;

	/**
	 * Set up a description
	 *
	 * @param string         $main   Main event description
	 * @param string         $title  Event title
	 * @param Grabber_Genres $genres Event genres
	 */
	function __construct($main = null, $title = null, Grabber_Genres $genres = null) {
		if ($main) {
			$this->_main = $this->_parseString($main);
		}
		if ($title) {
			$this->_title = $this->_parseString($title);
		}
		if ($genres) {
			$this->_genres = $genres;
		}
	}

	private function _parseString($str) {
		$str = strip_tags($str);
		$str = preg_replace('/\r?\n\r?/', ' ', $str);
		$str = strip_tags($str);
		$str = preg_replace('#\[(.+?)\]#', '($1)', $str);
		$str = preg_replace('#\bdj[\'`]s\b#', 'DJs', $str);
		$str = preg_replace('/[\:\.]$/', '', $str);
		$str = preg_replace('/\b([A-ZÖÄÜ])([A-ZÖÄÜ]{2,})\b/e', "'\\1'.strtolower('\\2')", $str);
		$str = preg_replace('/\s+/u', ' ', $str);
		$str = trim($str);
		return $str;
	}

	private function _endOnPunctuation($str, $character = '.') {
		if (empty($str)) {
			return '';
		}
		$end = substr($str, -1);
		if (strrpos('.!?:', $end) === false) {
			$str .= $character;
		}
		return $str;
	}

	public function __toString() {
		$description = '';
		if ($this->_title) {
			$description .= ucfirst(substr($this->_title, 0, 80));
		}
		if ($this->_main) {
			$description = $this->_endOnPunctuation($description, ':');
			$description .= ' ';
			$description .= ucfirst(substr($this->_main, 0, 500));
		}
		if ($this->_genres && $this->_genres->count() > 0) {
			$description = $this->_endOnPunctuation($description);
			$description .= ' ';
			$description .= substr($this->_genres, 0, 100);
		}
		return $description;
	}
}
