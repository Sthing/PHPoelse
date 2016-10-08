<?php

namespace Standard\User;

/**
 * User object
 *
 * @author jdg
 */
class User {
	
	/**
	 *
	 * @var int
	 */
	protected $id;
	
	/**
	 *
	 * @var string
	 */
	protected $alias;
	
	/**
	 * 
	 * @param int $id
	 * @param string $alias
	 */
	public function __construct(int $id, string $alias) {
		$this->id = $id;
		$this->alias = $alias;
	}
	
	/**
	 * Gets the user ID
	 */
	public function getId() : int {
		return $this->id;
	}
	
	/**
	 * Get the user alias
	 */
	public function getAlias() : string {
		return $this->alias;
	}
}
