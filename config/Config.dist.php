<?php

require_once 'Zend/Date.php';

/**
 * Config class
 *
 */
class Config {

	/**
	 * Create a Config-object.
	 *
	 * All configuration should be done here
	 */
	public function __construct() {

		$this->debug = true;

		$this->morninghour = 6;

		$this->domain = 'www.denkmal.dev';

		$this->loginkey = 'YourSecretKeyForApiLogin';

		$this->ferienEnd = new Zend_Date(array('year' => 2012, 'month' => 9, 'day' => 28, 'hour' => 12));

		$this->grabber = new stdClass();
		$this->grabber->debug = false;
		$this->grabber->defaultTime = '22:00:00';
		$this->grabber->tresholdNextyear = 4; // Events longer in the past (in months) are assumed to be next year
		$this->grabber->maxDays = 14; // How far in the future an event can be for inserting
		$this->grabber->days = 10; // How much to grab for Grabber_Calendar-classes
		$this->grabber->disableWithin = 7; // Disable new/updated events within next X days

		$this->googleanalytics = new stdClass();
		$this->googleanalytics->track = true;
		$this->googleanalytics->code = 'UA-XXXXXX-X';

		$this->cache = new stdClass();
		$this->cache->enabled = true;
		$this->cache->lifetime = 86400;
		$this->cache->prefix = 'Denkmal_';

		$this->db = new stdClass();
		$this->db->adapter = 'PDO_MYSQL';
		$this->db->config = array('host'     => 'localhost',
								  'dbname'   => 'denkmal',
								  'username' => 'root',
								  'password' => 'root',
		);

		$this->tmp = '../tmp';
	}
}
