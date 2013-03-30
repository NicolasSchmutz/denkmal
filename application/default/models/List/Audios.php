<?php

require_once 'List/Abstract.php';
require_once 'Audio.php';


/**
 * List_Audios Model
 *
 */
class List_Audios extends List_Abstract
{
	const TYPE_ALL = self::TYPE_DEFAULT;
	const TYPE_SUGGESTIONS = 2;				// Suggestions for an event


	/**
	 * Load audios
	 *
	 */
	protected function _load() {
		switch ($this->_type) {
			case self::TYPE_ALL:
				$this->_items = $this->_getTypeAll();
				break;
			case self::TYPE_SUGGESTIONS:
				$this->_items = $this->_getTypeSuggestions($this->_filter);
				break;
			default:
				throw new Denkmal_Exception('Invalid audios-list type (' .$this->_type. ')');
				break;
		}
	}

	/**
	 * Return all audios
	 *
	 * @return array Audios: array(0 => '54 nude honeys - where is love.mp3')
	 */
	private function _getTypeAll() {
		$items = array();
		$directory = opendir('audio');
		while ($file = readdir($directory)) {
			if (@strripos($file, '.mp3', 4) !== false) {
				$items[] = $file;
			}
		}
		closedir($directory);

		sort($items);

		return $items;
	}


	/**
	 * Return audios suggested for an event
	 *
	 * @param Event $event Event to suggest audios for
	 * @return array Audios: array(0 => '54 nude honeys - where is love.mp3')
	 */
	private function _getTypeSuggestions($event) {
		// Get linked words in event-description
		require_once 'List/Urls.php';
		$urls = new List_Urls();
		$words = $urls->strMatches($event->getDescription());

		// Get all audios and find matches with event-description-words
		$allAudios = new List_Audios();
		$items = array();
		foreach ($allAudios->get() as $audio) {
			foreach ($words as $word) {
				$word = Audio::toFilename($word);
				if (stripos($audio,$word) !== false) {
					$items[] = $audio;
					break;
				}
			}
		}

		sort($items);

		return $items;
	}

}
