<?php

require_once 'Promotion.php';


/**
 * Promotion Entry
 *
 */
class PromotionEntry
{
	private $_data = array();

	function __construct($id = null) {
		if (isset($id)) {
			$this->_load($id);
		}
	}

	/**
	 * Load a promotion entry's properties
	 *
	 * @param int $id The promotion entry's id
	 */
	private function _load($id) {
		$id = abs(intval($id));
		$db = Denkmal_Db::get();
		$sql = 'SELECT id, promotionId, name, email
				FROM promotion_entry
				WHERE id=?';
		$this->_data = $db->fetchRow($sql, $id);

		if (!$this->_data) {
			throw new Denkmal_Exception("Promotion entry doesn't exist (" .$id.")");
		}
	}

	/**
	 * Return the promotion entry's id
	 *
	 * @return int Id
	 */
	public function getId() {
		if (isset($this->_data['id'])) {
			return intval($this->_data['id']);
		}
		return null;
	}

	/**
	 * Return the promotion entry's Promotion
	 *
	 * @return Promotion Promotion
	 */
	public function getPromotion() {
		if (isset($this->_data['promotionId'])) {
			return new Promotion(intval($this->_data['promotionId']));
		}
		return null;
	}

	/**
	 * Return the promotion entry's name
	 *
	 * @return string Name
	 */
	public function getName() {
		if (isset($this->_data['name'])) {
			return $this->_data['name'];
		}
		return null;
	}

	/**
	 * Return the promotion entry's email
	 *
	 * @return string Email
	 */
	public function getEmail() {
		if (isset($this->_data['email'])) {
			return $this->_data['email'];
		}
		return null;
	}


	/**
	 * Set name
	 *
	 * @param string $name Name
	 * @return boolean True on success
	 */
	public function setName($name) {
		$name = strip_tags($name);
		$name = trim($name);

		$this->_data['name'] = $name;
		return true;
	}

	/**
	 * Set email
	 *
	 * @param string $email E-Mail
	 * @return boolean True on success
	 */
	public function setEmail($email) {
		$email = strip_tags($email);
		$email = trim($email);

		if ($promotion = $this->getPromotion()) {
			$db = Denkmal_Db::get();
			$exists = $db->fetchOne('SELECT COUNT(1)
										FROM promotion_entry
										WHERE promotionId=? AND email=?', array($promotion->getId(), $email));
			if ($exists) {
				return false;
			}
		}

		$this->_data['email'] = $email;
		return true;
	}

	/**
	 * Set promotion
	 *
	 * @param Promotion $promotion OPTIONAL Promotion
	 * @return boolean True on success
	 */
	public function setPromotion($promotion = null) {
		if (!$promotion) {
			$promotion = Promotion::getActivePromotion();
		}
		if (!$promotion) {
			return false;
		}
		$this->_data['promotionId'] = $promotion->getId();
		return true;
	}


	/**
	 * Save/create this promotion entry
	 */
	public function save() {
		$db = Denkmal_Db::get();
		$data = $this->_data;

		if ($this->getId() === null) {
			// Create promotion entry
			if (!$promotion = Promotion::getActivePromotion()) {
				throw new Denkmal_Exception('No promotion is active');
			}
			$data['promotionId'] = $promotion->getId();

			$db->insert('promotion_entry', $data);
			$this->_load($db->lastInsertId('promotion_entry'));
		} else {
			// Update promotion entry
			$db->update('promotion_entry', $data, 'id='.$this->getId());
		}

		Denkmal_Cache::clean();
	}
}
