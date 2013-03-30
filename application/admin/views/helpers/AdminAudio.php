<?php



/**
 * View-Helper to display an audio (in list)
 *
 */
class Zend_View_Helper_AdminAudio extends Zend_Controller_Action_Helper_Abstract
{
	
	
	/**
	 * Return an audio
	 *
	 * @param string $audio Audio-file
	 * @return string HTML for the audio in a list
	 */	
	public function adminAudio($audio) {
		$html = '';
		
		$html .= '<a href="javascript:;">' .$audio. '</a>';
		
		return $html;
	}
}