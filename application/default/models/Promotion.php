<?php



/**
 * Promotion
 *
 */
class Promotion {

	private $_data = array();

	function __construct($id = null) {
		if (isset($id)) {
			$this->_load($id);
		}
	}

	/**
	 * Load a promotion's properties
	 *
	 * @param int $id The promotion's id
	 */
	private function _load($id) {
		$id = abs(intval($id));
		$db = Denkmal_Db::get();
		$sql = 'SELECT id, text, active
				FROM promotion
				WHERE id=?';
		$this->_data = $db->fetchRow($sql, $id);

		if (!$this->_data) {
			throw new Denkmal_Exception("Promotion doesn't exist (" . $id . ")");
		}
	}

	/**
	 * Return the promotion's id
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
	 * Return the promotion's text
	 *
	 * @return string Text
	 */
	public function getText() {
		if (isset($this->_data['text'])) {
			return $this->_data['text'];
		}
		return null;
	}

	/**
	 * Return whether the promotion is active
	 *
	 * @return boolean Active
	 */
	public function getActive() {
		if (isset($this->_data['active']) && $this->_data['active']) {
			return true;
		}
		return false;
	}

	/**
	 * Return number of entries
	 *
	 * @return int Number of entries
	 */
	public function getEntriesNum() {
		$db = Denkmal_Db::get();
		return $db->fetchOne('SELECT COUNT(1)
								FROM promotion_entry e
								WHERE e.promotionId=?'
			, array($this->getId()));
	}

	/**
	 * Return entries
	 *
	 * @return array Entries
	 */
	public function getEntries() {
		require_once 'List/PromotionEntries.php';
		return new List_PromotionEntries(List_PromotionEntries::TYPE_PROMOTION, $this);
	}

	/**
	 * Return thanks/success text
	 *
	 * @return string Thanks text
	 */
	public function getTextThanks() {
		return 'Vielen Dank fürs Mitmachen.';
	}

	/**
	 * Return whether the current user (cookie) has already submitted
	 *
	 * @return boolean Has submitted
	 */
	public function getHasSubmitted() {
		@$cookie = $_COOKIE['promotion_' . $this->getId()];
		return (bool) $cookie;
	}

	/**
	 * Return current active promotion
	 *
	 * @return Promotion Active promotion OR false
	 */
	public static function getActivePromotion() {
		$db = Denkmal_Db::get();
		$id = $db->fetchOne('SELECT id
								FROM promotion p
								WHERE p.active=1
								LIMIT 1');
		if ($id) {
			return new Promotion ($id);
		} else {
			return false;
		}
	}

	/**
	 * Set active
	 *
	 * @param boolean $active Active
	 * @return boolean True on success
	 */
	public function setActive($active) {
		$this->_data['active'] = (int) (boolean) $active;
		return true;
	}

	/**
	 * Save/create this event
	 */
	public function save() {
		$db = Denkmal_Db::get();

		$data = $this->_data;

		if ($data['active']) {
			// Deactivate all other promotions
			$db->update('promotion', array('active' => false));
		}

		if ($this->getId() === null) {
			// Create promotion
			$db->insert('promotion', $data);
			$this->_load($db->lastInsertId('promotion'));
		} else {
			// Update promotion
			$db->update('promotion', $data, 'id=' . $this->getId());
		}

		Denkmal_Cache::clean();
	}
}
