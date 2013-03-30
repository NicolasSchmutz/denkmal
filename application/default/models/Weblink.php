<?php


/**
 * Weblink
 *
 */
class Weblink {

	private $_url, $_description;

	/**
	 * Construct a weblink
	 *
	 * @param string $url         URL
	 * @param string $description Description
	 */
	function __construct($url, $description) {
		$this->_url = $url;
		$this->_description = $description;
	}

	/**
	 * Return the link's url
	 *
	 * @return string URL
	 */
	public function getUrl() {
		return $this->_url;
	}

	/**
	 * Return the link's description
	 *
	 * @return string Description
	 */
	public function getDescription() {
		return $this->_description;
	}

	public function getDomain() {
		$domain = $this->getUrl();
		$domain = preg_replace(array('/^https?:\/\//', '/^www\./', '/\/.*$/'), null, $domain);
		return $domain;
	}
}
