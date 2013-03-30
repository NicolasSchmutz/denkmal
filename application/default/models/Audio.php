<?php
/**
 * Audio
 *
 */
class Audio
{
	/**
	 * Return a audio-filename for a string ("The Möles - What's up?" => "the moles - whats up")
	 * @param string $str Input-string
	 * @return string Audio-filename
	 */
	public static function toFilename($str) {
		// Make string lower case
		$str = mb_strtolower($str);
		
		// Replace common non-ascii chars
		$str = str_replace(array('ö','ä','ü'), array('oe','ae','ue'), $str);
		$str = str_replace('&', 'and', $str);
		
		// Convert special signs like 'é' to ascii 'e' (transliterating)
		// This needs a UTF8-locale. We set this, but back the original one up, to revert afterwards
		$localeBackup = setlocale(LC_CTYPE, 0);
		setlocale(LC_CTYPE, "en_US.UTF8");
		
		$str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
		setlocale(LC_CTYPE, $localeBackup);
		
		// Remove unallowed signs
		$str = preg_replace('/([^\w-\. ])/', '', $str);
		
		return $str;
	}

}