<?php



/**
 * Cache access.
 * Wraps Zend_Cache
 *
 */
class Denkmal_Cache {

	private static $_prefix;

	/**
	 * External cache (apc or filecache)
	 *
	 * @var Zend_Cache_Core
	 */
	private static $_externalCache;

	/**
	 * Internal cache
	 *
	 * @var array
	 */
	private static $_internalCache = array();

	/**
	 * Create and return the Cache-object
	 *
	 * @return Zend_Cache_Core The cache-object
	 */
	public static function get() {
		if (!isset(self::$_externalCache)) {
			self::_setUp();
		}
		return self::$_externalCache;
	}

	/**
	 * Test if a cache is available for the given id and (if yes) return it (false else).
	 * Proxies to Zend_Cache::load()
	 *
	 * @param string $id Cache id
	 * @return mixed Cached data (or false)
	 */
	public static function load($id) {
		if (isset(self::$_internalCache[$id])) {
			return self::$_internalCache[$id];
		}
		@$data = self::get()->load(self::$_prefix . $id);
		self::$_internalCache[$id] = $data;
		return $data;
	}

	/**
	 * Save some data in cache.
	 * Proxies to Zend_Cache::save()
	 *
	 * @param mixed  $data             Data to put in cache
	 * @param string $id               Cache id
	 * @param int    $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
	 * @return boolean True if no problem
	 */
	public static function save($data, $id, $specificLifetime = false) {
		self::$_internalCache[$id] = $data;
		return self::get()->save($data, self::$_prefix . $id, array(), $specificLifetime);
	}

	/**
	 * Remove a cache
	 * Proxies to Zend_Cache::remove()
	 *
	 * @param string $id Cache id to remove
	 * @return boolean True if ok
	 */
	public static function remove($id) {
		unset(self::$_internalCache[$id]);
		return self::get()->remove(self::$_prefix . $id);
	}

	/**
	 * Clean the whole cache
	 *
	 * @return boolean True on success
	 */
	public static function clean() {
		self::$_internalCache = array();
		return self::get()->clean(Zend_Cache::CLEANING_MODE_ALL);
	}

	/**
	 * Set up the Cache-object.
	 *
	 * @return Zend_Cache_Core The cache-object
	 * @throws My_Exception If creation fails
	 */
	private static function _setUp() {
		$config = Zend_Registry::get('config');
		self::$_prefix = $config->cache->prefix;
		$frontendOptions = array(
			'caching'                 => $config->cache->enabled,
			'lifetime'                => $config->cache->lifetime,
			'automatic_serialization' => true,
		);
		try {
			if (extension_loaded('apc')) {
				// Use APC
				$cache = Zend_Cache::factory('Core', 'Apc', $frontendOptions);
			} else {
				// Use File-Cache
				$backendOptions = array(
					'cache_dir' => $config->tmp,
				);
				$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
			}
		} catch (Exception $e) {
			require_once 'Exception.php';
			throw new Denkmal_Exception('Cannot setup cache: ' . $e->getMessage());
		}
		self::$_externalCache = $cache;
		return $cache;
	}
}
