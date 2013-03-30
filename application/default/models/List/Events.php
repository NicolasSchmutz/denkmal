<?php

require_once 'List/Abstract.php';
require_once 'Event.php';
require_once 'Day.php';

/**
 * List_Events Model
 *
 */
class List_Events extends List_Abstract {

	const TYPE_DAY = self::TYPE_DEFAULT;
	const TYPE_DAY_BLOCKED = 2;
	const TYPE_SEARCH = 3;
	const TYPE_LOCATION = 4;
	const TYPE_LOCATION_ALL = 5; // Also blocked and disabled
	const TYPE_DISABLED = 6;

	/**
	 * Load events
	 *
	 */
	protected function _load() {
		switch ($this->_type) {
			case self::TYPE_DAY:
				$this->_items = $this->_getTypeDay($this->_filter);
				break;
			case self::TYPE_DAY_BLOCKED:
				$this->_items = $this->_getTypeDayBlocked($this->_filter);
				break;
			case self::TYPE_SEARCH:
				$this->_items = $this->_getTypeSearch($this->_filter);
				break;
			case self::TYPE_LOCATION:
				$this->_items = $this->_getTypeLocation($this->_filter);
				break;
			case self::TYPE_LOCATION_ALL:
				$this->_items = $this->_getTypeLocationAll($this->_filter);
				break;
			case self::TYPE_DISABLED:
				$this->_items = $this->_getTypeDisabled();
				break;
			default:
				throw new Denkmal_Exception('Invalid events-list type (' . $this->_type . ')');
				break;
		}
	}

	/**
	 * Return events by day
	 *
	 * @param Day $day Day for which to get events
	 * @return array Events
	 */
	private function _getTypeDay($day) {
		$cacheId = 'list_events_day_' . $day->getDate()->toString('y_MM_dd');
		$sql = 'SELECT e.id
				FROM event e, location l
				WHERE e.locationId = l.id
					AND e.enabled = 1
					AND e.blocked = 0
					AND e.from >= ?
					AND e.from <= ?
				ORDER BY e.star DESC, l.id';
		$morninghour = Zend_Registry::get('config')->morninghour;
		$date = clone($day->getDate());
		$args = array($date->addHour($morninghour)->toString('y-MM-dd HH:mm:ss'),
			$date->addDay(1)->toString('y-MM-dd HH:mm:ss'));
		if (false === ($items = Denkmal_Cache::load($cacheId))) {
			// Cache miss
			$ids = Denkmal_Db::get()->fetchCol($sql, $args);
			$items = array();
			foreach ($ids as $id) {
				$items[] = new Event($id);
			}
			Denkmal_Cache::save($items, $cacheId);
		}

		return $items;
	}

	/**
	 * Return blocked events by day
	 *
	 * @param Day $day Day for which to get blocked events
	 * @return array Events
	 */
	private function _getTypeDayBlocked($day) {
		$cacheId = 'list_events_day_blocked_' . $day->getDate()->toString('y_MM_dd');
		$sql = 'SELECT e.id
				FROM event e, location l
				WHERE e.locationId = l.id
					AND e.enabled = 1
					AND e.blocked = 1
					AND e.from >= ?
					AND e.from <= ?
				ORDER BY e.star DESC, l.id';
		$morninghour = Zend_Registry::get('config')->morninghour;
		$date = clone($day->getDate());
		$args = array($date->addHour($morninghour)->toString('y-MM-dd HH:mm:ss'),
			$date->addDay(1)->toString('y-MM-dd HH:mm:ss'));

		if (false === ($items = Denkmal_Cache::load($cacheId))) {
			// Cache miss
			$ids = Denkmal_Db::get()->fetchCol($sql, $args);
			$items = array();
			foreach ($ids as $id) {
				$items[] = new Event($id);
			}
			Denkmal_Cache::save($items, $cacheId);
		}

		return $items;
	}

	/**
	 * Return events by search
	 *
	 * @param string $q Search-query
	 * @return array Events
	 */
	private function _getTypeSearch($q) {
		$db = Denkmal_Db::get();
		$sql = "(SELECT e.id, e.from
				FROM event e
				WHERE e.enabled=1 AND e.blocked=0 AND e.id > 0 AND MATCH (e.description) AGAINST(? IN BOOLEAN MODE)
				)
				UNION
				(SELECT e.id, e.from
				FROM event e, location l
				WHERE e.locationId=l.id AND e.enabled=1 AND e.blocked=0 AND e.id > 0 AND MATCH(l.name) AGAINST(? IN BOOLEAN MODE)
				)
				ORDER BY `from` DESC
				LIMIT 15";
		$args = array($this->_getSearchQuery($q), $this->_getSearchQuery($q));

		$ids = $db->fetchCol($sql, $args);
		$items = array();
		foreach ($ids as $id) {
			$items[] = new Event($id);
		}

		return $items;
	}

	/**
	 * Return upcoming events by location
	 *
	 * @param Location $location Location
	 * @return array Events
	 */
	private function _getTypeLocation($location) {
		$db = Denkmal_Db::get();
		$sql = "SELECT e.id
				FROM event e
				WHERE e.enabled=1
					AND e.blocked=0
					AND e.locationId=?
					AND e.from >= ?
				ORDER BY e.`from` ASC
				LIMIT 10";
		$args = array($location->getId(), Day::now()->getDate()->toString('y-MM-dd'));

		$ids = $db->fetchCol($sql, $args);
		$items = array();
		foreach ($ids as $id) {
			$items[] = new Event($id);
		}

		return $items;
	}

	/**
	 * Return all upcoming events by location
	 *
	 * @param Location $location Location
	 * @return array Events
	 */
	private function _getTypeLocationAll($location) {
		$db = Denkmal_Db::get();
		$sql = "SELECT e.id
				FROM event e
				WHERE e.locationId=?
					AND e.from >= ?
				ORDER BY e.`from` ASC";
		$args = array($location->getId(), Day::now()->getDate()->toString('y-MM-dd'));

		$ids = $db->fetchCol($sql, $args);
		$items = array();
		foreach ($ids as $id) {
			$items[] = new Event($id);
		}

		return $items;
	}

	/**
	 * Return disabled upcoming events
	 *
	 * @return array Events
	 */
	private function _getTypeDisabled() {
		$db = Denkmal_Db::get();
		$sql = "SELECT e.id
				FROM event e
				WHERE e.enabled=0
					AND e.from >= ?
				ORDER BY e.`from` ASC";
		$args = array(Day::now()->getDate()->toString('y-MM-dd'));

		$ids = $db->fetchCol($sql, $args);
		$items = array();
		foreach ($ids as $id) {
			$items[] = new Event($id);
		}

		return $items;
	}

	/**
	 * Return a FULLTEXT-query from a user-query string
	 *
	 * @param string $q User-query
	 * @return string FULLTEXT-query
	 */
	private function _getSearchQuery($q) {
		$q = preg_replace('#(\s|^)([^"-])#', ' +$2', $q);
		return $q;
	}
}
