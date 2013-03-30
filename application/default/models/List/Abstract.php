<?php

require_once 'Denkmal/Cache.php';


/**
 * Abstract class for lists
 *
 */ 
abstract class List_Abstract  implements Iterator
{
	
	/**
	 * Ihe itemlist of this list.
	 * Filled by {@link _load()}.
	 */
	protected $_items = null;
	
	protected $_type, $_filter;
	
	private $_position = 0;
	
	const TYPE_DEFAULT = 1;	


	/**
	 * Constructor. Set up the list
	 * 
	 * @param int $type OPTIONAL Request-type
	 * @param mixed $filter OPTIONAL Filter results
	 */ 
	function __construct($type = self::TYPE_DEFAULT, $filter = null) {
		$this->_type = intval($type);
		$this->_filter = $filter;
		$this->_load();
	}
	
	/**
	 * Load a list of items from DB or Cache into $this->_items.
	 * 
	 */
	abstract protected function _load();

	
	/**
	 * Return the items
	 * 
	 * @return array Hash-array of the items
	 */
	function get() {
		return $this->_items;
	}

	/**
	 * Return the number of items.
	 * 
	 * @return int Number of items
	 */
	function num() {
		return count($this->get());
	}
	
	
	/**
	 * Iterator-function "rewind"
	 */
	function rewind() {
		$this->_position = 0;
	}
	
	/**
	 * Iterator-function "current"
	 * @return mixed Current item
	 */
	function current() {
		return $this->_items[$this->_position];
	}
	
	/**
	 * Iterator-function "key"
	 * @return int Current position
	 */
	function key() {
		return $this->_position;
	}
	
	/**
	 * Iterator-function "next"
	 */
	function next() {
		$this->_position++;
	}
	
	/**
	 * Iterator-function "valid"
	 * @return boolean If current position is valid
	 */
	function valid() {
		return isset($this->_items[$this->_position]);
	}
	
}
