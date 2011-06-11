<?php
/**
 * 
 *
 * @author Corey Frang
 * @package EpicDb_Auth
 * @subpackage Group
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Auth_Group_Moderators
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Auth
 * @subpackage Group
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Auth_Group_Moderators extends MW_Auth_Group_Abstract {
  	protected $_groupDescription = 'Moderators';
	protected $_groupName = 'moderators';

	/**
	 * Class Instance - Singleton Pattern
	 *
	 * @var self
	 **/
	static protected $_instance = NULL;

	/**
	 * private constructor - singleton pattern
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	private function __construct()
	{
	}

	/**
	 * Returns (or creates) the Instance - Singleton Pattern
	 *
	 * @return self
	 * @author Corey Frang
	 **/
	static public function getInstance()
	{
		if (self::$_instance === NULL) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

}