<?php


/**
 * Action-Helper to format data which is sent to client (json)
 */
class Denkmal_Action_Helper_Data extends Zend_Controller_Action_Helper_Abstract {

	/**
	 * Return events-data
	 *
	 * @param List_Events $events Events
	 * @return array Events-data
	 */
	function events($events) {
		$data = array();
		foreach ($events as $event) {
			$location = $event->getLocation();
			$data[$event->getId()] = array('locationId' => $location->getId(), 'star' => $event->getStar());
		}
		return $data;
	}

	/**
	 * Return locations-data
	 *
	 * @param List_Locations $locations Locations
	 * @return array Locations-data
	 */
	function locations($locations) {
		$data = array();
		foreach ($locations as $location) {
			if ($position = $location->getPosition()) {
				$data[$location->getId()] =
						array('name' => $location->getName(),
							  'url'  => $location->getUrl(),
							  'show' => $location->getShowalways(),
							  'x'    => $position->getX(),
							  'y'    => $position->getY(),
						);
			}
		}
		return $data;
	}

	/**
	 * Return audioplayer-data
	 *
	 * @param List_Events $events Events for audio-playback
	 * @return array Audiplayer-data
	 */
	function audios($events) {
		$data = array();
		foreach ($events as $event) {
			if ($event->getAudio()) {
				$data[md5($event->getAudio())] = $event->getAudio();
			}
		}
		return $data;
	}
}
