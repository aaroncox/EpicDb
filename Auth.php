<?php
/**
 *
 *
 * @author Corey Frang
 * @package R2Db_Auth
 * @copyright Copyright (c) 2010 Momentum Workshop, Inc
 */

/**
 *  R2Db_Auth
 *
 * undocumented
 *
 * @author Corey Frang
 * @package R2Db_Auth
 * @copyright Copyright (c) 2010 Momentum Workshop, Inc
 * @version $Id: Auth.php 249 2010-11-13 03:52:47Z root $
 */
class EpicDb_Auth extends MW_Auth {

	/**
	 * private constructor - singleton pattern
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	protected function __construct()
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


	/**
	 * cache the result of the logged in users profile
	 *
	 * @var _profile
	 **/
	protected $_profile = null;
	public function getUserProfile()
	{
		if ($this->_profile) return $this->_profile;

		$user = $this->getUser();

		$this->_profile = EpicDb_Mongo::db('user')->getProfile($this->getUser());

		return $this->_profile;
	}

	/**
	 * getUserRoles - undocumented function
	 *
	 * @return array of roles
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getUserRoles(MW_Auth_Mongo_Role $role = null, $recursed = false)
	{
		if (!$role) {
			if (!$role = $this->getUser()) {
				$role = MW_Auth_Mongo_Role::getGroup(MW_Auth_Group_Guest::getInstance());
			}
		}
		$return = array($role);
		// var_dump($role);
		foreach ($role->roles as $newRole)
		{
			if ($newRole) {
				$roles = $this->getUserRoles($newRole, true);
				$return = array_merge($return, $roles);
			}
		}
		if (!$recursed) {
			foreach ($this->getUserLevelRoles( EpicDb_Mongo::db('user')->getProfile( $role ) ) as $userRole) {
				$return[] = MW_Auth_Mongo_Role::getGroup( $userRole );
			}
		}
		return $return;
	}

	protected function _makeActionHelper() {
		return new EpicDb_Auth_Controller_Action_Helper();
	}

	/**
	 * Returns the ACL or creates one.
	 *
	 * @return MW_Auth_Acl
	 * @author Corey Frang
	 **/
	protected function _getACL()
	{
		if ($this->_acl) return $this->_acl;
		$user = $this->getUser();
		if ( $user ) {
			$profile = $this->getUserProfile();
		}
		$userid = $user?$user->id:'guest';

		$this->_acl = new MW_Auth_Acl();
		$this->getBackend()->loadRolesFromUser($user, $this->_acl, $user ? $this->getUserLevelRoles( $profile ) : array() );
		return $this->_acl;
	}

	public function getUserLevelRoles( $user )
	{
		$roles = array();
		if ( $user instanceOf EpicDb_Mongo_Profile_User ) {
			$level = $user->getLevel();
			if ( $level >= 2 ) {
				$roles[] = EpicDb_Auth_Group_UpVoters::getInstance();
			}
			if ( $level >= 3 ) {
				$roles[] = EpicDb_Auth_Group_DownVoters::getInstance();
				$roles[] = EpicDb_Auth_Group_Flaggers::getInstance();
			}
			if ( $level >= 14 ) {
				$roles[] = EpicDb_Auth_Group_TagCreators::getInstance();
			}
		}
		return $roles;
	}

}