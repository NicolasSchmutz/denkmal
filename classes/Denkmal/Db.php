<?php



/**
 * Database connection
 *
 */
class Denkmal_Db {

	private static $_db;

	/**
	 * Creates the db-connection if it doesn't already exist and returns it.
	 *
	 * @return Zend_Db_Adapter_Abstract The created or current database-adapter
	 */
	public static function get() {
		if (!isset(self::$_db)) {
			self::_setUp();
		}
		return self::$_db;
	}

	/**
	 * Connect to the database and return the created adapter
	 *
	 * @return Zend_Db_Adapter_Abstract The created database-adapter
	 * @throws Exception If connecting is impossible
	 */
	private static function _setUp() {
		$config = Zend_Registry::get('config');
		if (isset(self::$_db)) {
			try {
				self::$_db->closeConnection();
			} catch (Zend_Exception $e) {
				// Could not close current db-connection, ignore that..
			}
		}
		try {
			$db = Zend_Db::factory($config->db->adapter, $config->db->config);
			Zend_Db_Table::setDefaultAdapter($db);
			$db->getConnection();
			$db->query('SET NAMES "utf8"');
		} catch (Zend_Exception $e) {
			require_once 'Exception.php';
			throw new Denkmal_Exception('DB Error: ' . $e);
		}
		self::$_db = $db;
		return $db;
	}

	/**
	 * Return the given value OR a Db-Expr(NULL), if $value===null
	 *
	 * @param mixed $value The value
	 * @return mixed The value (or Db-Expr(NULL) if $value is null)
	 */
	public static function valueOrNull($value) {
		if ($value === null) {
			return new Zend_Db_Expr('NULL');
		} else {
			return $value;
		}
	}
}
