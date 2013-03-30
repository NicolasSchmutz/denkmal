<?php

/**
 * Geographical Position
 *
 */
class Position {

	private $_latitude, $_longitude;

	private static $_mapCoords = array('xFrom' => 0, 'xTo' => 389, 'lonFrom' => 7.554718, 'lonTo' => 7.68951,
									   'yFrom' => 32, 'yTo' => 351, 'latFrom' => 47.596949, 'latTo' => 47.519354);
	private static $_mapSize = array('xMin' => 0, 'xMax' => 380, 'yMin' => 15, 'yMax' => 352);

	/**
	 * Construct a position
	 *
	 * @param float $latitude  Latitude
	 * @param float $longitude Longitude
	 */
	function __construct($latitude, $longitude) {
		$this->_latitude = floatval($latitude);
		$this->_longitude = floatval($longitude);
	}

	/**
	 * Return the position's latitude
	 * @return float Latitude
	 */
	public function getLatitude() {
		return $this->_latitude;
	}

	/**
	 * Return the position's longitude
	 * @return float longitude
	 */
	public function getLongitude() {
		return $this->_longitude;
	}

	/**
	 * Return the X-coord for the map in pixels
	 * @return int X-coord
	 */
	public function getX() {
		$x = round(($this->getLongitude() - self::$_mapCoords['lonFrom'])
				/ (self::$_mapCoords['lonTo'] - self::$_mapCoords['lonFrom'])
				* (self::$_mapCoords['xTo'] - self::$_mapCoords['xFrom'])
				+ self::$_mapCoords['xFrom']);
		if ($x < self::$_mapSize['xMin']) {
			$x = self::$_mapSize['xMin'];
		}
		if ($x > self::$_mapSize['xMax']) {
			$x = self::$_mapSize['xMax'];
		}
		return $x;
	}

	/**
	 * Return the Y-coord for the map in pixels
	 * @return int Y-coord
	 */
	public function getY() {
		$y = round(($this->getLatitude() - self::$_mapCoords['latFrom'])
				/ (self::$_mapCoords['latTo'] - self::$_mapCoords['latFrom'])
				* (self::$_mapCoords['yTo'] - self::$_mapCoords['yFrom'])
				+ self::$_mapCoords['yFrom']);
		if ($y < self::$_mapSize['yMin']) {
			$y = self::$_mapSize['yMin'];
		}
		if ($y > self::$_mapSize['yMax']) {
			$y = self::$_mapSize['yMax'];
		}
		return $y;
	}
}
