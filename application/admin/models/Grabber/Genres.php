<?php

require_once 'Grabber/String.php';

/**
 * Genres-list used by grabber
 *
 */ 
class Grabber_Genres
{
	private $_genres = array();
	
	
	/**
	 * Set up a genres-list
	 * 
	 * @param string $genres Genres list as string
	 */
	function __construct($genres) {
		$genres = new Grabber_String($genres);
		foreach ($genres->split('#[,|/]#') as $genre) {
			if ($genre = strtolower(trim($genre))) {
				$this->_genres[] = $genre;
			}
		}
	}
	
	public function count() {
		return count($this->_genres);
	}
	
	public function __toString() {
		$genres = $this->_genres;
		if (count($genres) > 0) {
			$genres[0] = ucfirst($genres[0]);
		}
		return implode(', ', $genres);
	} 
	
} 
